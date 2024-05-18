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
            <div><strong>Name:</strong> <span id="userName"></span></div>
            <div><strong>Address:</strong> <span id="userAddress"></span></div>
            <div><strong>Mobile Number:</strong> <span id="userMobile"></span></div>
            <div><strong>Birthday:</strong> <span id="userBirthday"></span></div>

            <div class="image-container" id="imageContainer">
              <div id="relatedImageContainer"><img id="relatedImage" class="relatedImageContainer"
                  src="./face/labels/Unknown/0.jpg"></div>
              <div id="confidence"></div>
              <div class="button-container">
                <form id="createLogsForm" method="post" action="face/createLog.php">
                  <button id="submitButton" type="submit" name="submitButton">Select</button>
                  <br>
                  <!-- <small>Wait for the result before you click the select button</small>
                  <input type="hidden" id="confidenceScoreInput" name="confidence_score">
                  <input type="hidden" id="userNameInput" name="user_name"> -->
                </form>
              </div>
            </div>
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
  const relatedImageContainer = document.getElementById("relatedImageContainer");
  const userNameSpan = document.getElementById("userName");
  const userAddressSpan = document.getElementById("userAddress");
  const userMobileSpan = document.getElementById("userMobile");
  const userBirthdaySpan = document.getElementById("userBirthday");
  const confidenceDisplay = document.getElementById("confidence");
  const submitButton = document.getElementById("submitButton");
  const displaySize = { width: video.width, height: video.height };

  document.addEventListener("DOMContentLoaded", function () {
    const submitButton = document.getElementById("submitButton");
    const confidenceScoreInput = document.getElementById("confidenceScoreInput");
    const userNameInput = document.getElementById("userNameInput");

    submitButton.addEventListener("click", () => {
      const confidenceScore = confidenceDisplay.innerText.split(": ")[1];
      const userName = userNameSpan.textContent;

      confidenceScoreInput.value = confidenceScore;
      userNameInput.value = userName;

      document.getElementById("createLogsForm").submit();
    });
  });

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
                  updateRelatedImage(relatedImageSrc);

                  displayUserDetails(bestMatch.label);
                  confidenceDisplay.innerText = `Confidence Score: ${parseFloat((bestMatchScore).toFixed(2))}`;
                  submitButton.disabled = false;
                } else {
                  submitButton.disabled = true;
                  userNameSpan.textContent = "Unknown";
                  userAddressSpan.textContent = "";
                  userMobileSpan.textContent = "";
                  userBirthdaySpan.textContent = "";
                  confidenceDisplay.innerText = "Confidence Score: N/A";
                  // Set the related image to the placeholder for "Unknown"
                  updateRelatedImage("./face/labels/Unknown/0.jpg");
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
                // console.log(`No face detected in image ${i} for label ${name}`);
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

  function updateRelatedImage(src) {
    const existingImage = document.querySelector("#relatedImage");
    if (existingImage) {
      existingImage.src = src;
    } else {
      const relatedImage = document.createElement("img");
      relatedImage.id = "relatedImage";
      relatedImage.src = src;
      relatedImageContainer.appendChild(relatedImage);
    }
  }

  function displayUserDetails(label) {
    try {
      const user = labelsArray.find(item => item.name === label);
      if (user) {
        userNameSpan.textContent = user.name;
        userAddressSpan.textContent = user.address;
        userMobileSpan.textContent = user.cellphone;
        const formattedBirthday = new Date(user.birthday).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
        userBirthdaySpan.textContent = formattedBirthday;
        const container = document.querySelector('.container');
      } else {
        userNameSpan.textContent = "";
        userAddressSpan.textContent = "";
        userMobileSpan.textContent = "";
        userBirthdaySpan.textContent = "";
      }
    } catch (error) {
      console.error('Error displaying user details:', error);
    }
  }

</script>

</html>