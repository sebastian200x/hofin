<?php

function decryptLabelsData($filePath, $key)
{
    $encryptedDataWithIV = file_get_contents($filePath);
    if ($encryptedDataWithIV !== false) {
        $iv_hex = substr($encryptedDataWithIV, 0, 32); // Extract IV from the beginning
        $encryptedData = substr($encryptedDataWithIV, 32); // Extract encrypted data without IV
        $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, hex2bin($iv_hex));
        if ($decryptedData !== false) {
            return $decryptedData;
        } else {
            echo '<script>console.log("Failed to decrypt data from labels.json.");</script>';
        }
    } else {
        echo '<script>console.log("Failed to read data from labels.json.");</script>';
    }
}

try {
    $labelsData = decryptLabelsData('./face/labels.json', 'Adm1n123');
    $labelsArray = json_decode($labelsData, true);
} catch (Exception $e) {
    echo '<script>console.log("' . $e->getMessage() . '");</script>';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Login</title>

    <script src="face-api.min.js"></script>
    <link rel="stylesheet" href="./styles/css/face_login.css">
    <?php include 'navbar.php'; ?>
</head>

<body>
    <div class="left">
        <img draggable="false" class="image" src="styles/images/logo-b.png" alt="logo">
    </div>
    <div class="right">
        <div class="centered">
            <div class="flex-container centered">
                <div class="container <?php echo strpos($address, 'Hulong duhat') !== false ? 'green-border' : ''; ?>">
                    <div class="video-container">
                        <video id="video" width="640" height="480" autoplay></video>
                        <canvas id="overlay" class="overlay"></canvas>
                        <div id="confidence"></div>

                    </div>
                    <a href="./index.php" class="login">Back</a>
                </div>
            </div>
        </div>
</body>

<script>
    const labelsArray = <?php echo json_encode($labelsArray); ?>;
    const video = document.getElementById("video");
    const overlay = document.getElementById("overlay");
    const confidenceDisplay = document.getElementById("confidence");

    const displaySize = { width: video.width, height: video.height };


    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("models"),
    ]).then(startWebcam);

    async function startWebcam() {
        try {
            navigator.mediaDevices
                .getUserMedia({
                    video: true,
                    audio: false,
                })
                .then((stream) => {
                    video.srcObject = stream;
                    video.onloadedmetadata = () => {
                        overlay.width = video.videoWidth;
                        overlay.height = video.videoHeight;

                        const labeledFaceDescriptors = getLabeledFaceDescriptions(labelsArray);
                        labeledFaceDescriptors.then((labeledFaceDescriptors) => {
                            const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

                            setInterval(async () => {
                                const detections = await faceapi
                                    .detectAllFaces(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5, minFaceSize: 100 }))
                                    .withFaceLandmarks()
                                    .withFaceDescriptors();

                                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                                const context = overlay.getContext("2d");
                                context.clearRect(0, 0, overlay.width, overlay.height);

                                let bestMatch = null;
                                let bestMatchScore = 0;

                                resizedDetections.forEach((detection, i) => {
                                    const result = faceMatcher.findBestMatch(detection.descriptor);
                                    const confidence = 1 - result.distance;
                                    if (confidence > bestMatchScore) {
                                        bestMatch = result;
                                        bestMatchScore = confidence;
                                    }

                                    const box = detection.detection.box;
                                    const drawBox = new faceapi.draw.DrawBox(box, {
                                        label: `${result.toString()} (${Math.round(confidence * 100)}% match)`,
                                    });
                                    drawBox.draw(context);
                                });

                                if (bestMatch && bestMatchScore >= 0.6) {
                                    const relatedImageSrc = `./face/labels/${bestMatch.label}/1.jpg`;

                                    confidenceDisplay.innerText = `Confidence Score: ${parseFloat((bestMatchScore).toFixed(2))}`;
                                } else {
                                    confidenceDisplay.innerText = "Confidence Score: N/A";
                                }
                            }, 100);
                        });
                    };
                })
                .catch((error) => {
                    console.error(error);
                });
        } catch (error) {
            console.error('Error starting webcam:', error);
        }
    }

    async function getLabeledFaceDescriptions(labels) {
        try {
            return Promise.all(
                labels.map(async (labelData) => {
                    const { name } = labelData;
                    console.log(name);
                    const descriptions = [];
                    for (let i = 0; i <= 2; i++) {
                        try {
                            let img;
                            try {
                                img = await faceapi.fetchImage(`./face/labels/${name}/${i}.jpg`);
                            } catch (jpgError) {
                                console.error(`Error fetching JPG image ${i} for label ${name}:`, jpgError);
                                img = await faceapi.fetchImage(`./face/labels/${name}/${i}.png`);
                            }
                            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                            if (detections) {
                                descriptions.push(detections.descriptor);
                            } else {
                                console.log(`No face detected in image ${i} for label ${name}`);
                            }
                        } catch (error) {
                            console.error(`Error fetching image ${i} for label ${name}:`, error);
                        }
                    }
                    return new faceapi.LabeledFaceDescriptors(name, descriptions);
                })
            );
        } catch (error) {
            console.error('Error fetching labeled face descriptions:', error);
            throw error;
        }
    }

</script>

</html>