// Function to start screen and webcam recording
async function startScreenAndWebcamRecording() {
    try {
      // Get screen recording permission
      const screenStream = await navigator.mediaDevices.getDisplayMedia({
        video: true,
      });
      console.log("Screen recording started.");
  
      // Get webcam and microphone permission
      const webcamStream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true,
      });
      console.log("Webcam and microphone access granted.");
  
      // Initialize two MediaRecorders for separate files
      const screenRecorder = new MediaRecorder(screenStream, {
        mimeType: "video/webm; codecs=vp9",
      });
      const webcamRecorder = new MediaRecorder(webcamStream, {
        mimeType: "video/webm; codecs=vp9",
      });
  
      let screenChunks = [];
      let webcamChunks = [];
  
      // Screen recording data handler
      screenRecorder.ondataavailable = function (event) {
        if (event.data.size > 0) {
          screenChunks.push(event.data);
        }
      };
  
      // Webcam recording data handler
      webcamRecorder.ondataavailable = function (event) {
        if (event.data.size > 0) {
          webcamChunks.push(event.data);
        }
      };
  
      // When both recorders stop, download the files
      screenRecorder.onstop = function () {
        const screenBlob = new Blob(screenChunks, { type: "video/webm" });
        const screenUrl = URL.createObjectURL(screenBlob);
        const a = document.createElement("a");
        a.style.display = "none";
        a.href = screenUrl;
        a.download = "screen-recording.webm";
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(screenUrl);
      };
  
      webcamRecorder.onstop = function () {
        const webcamBlob = new Blob(webcamChunks, { type: "video/webm" });
        const webcamUrl = URL.createObjectURL(webcamBlob);
        const a = document.createElement("a");
        a.style.display = "none";
        a.href = webcamUrl;
        a.download = "webcam-recording.webm";
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(webcamUrl);
      };
  
      // Start both recordings
      screenRecorder.start();
      webcamRecorder.start();
      console.log("Recording started.");
  
      // Stop recording after the exam finishes (modify time if needed)
      // Note: Add your own mechanism to stop the recording based on your quiz duration
      setTimeout(() => {
        screenRecorder.stop();
        webcamRecorder.stop();
        console.log("Recording stopped.");
      }, 10000); // Set according to your quiz time or auto-stop logic
  
    } catch (err) {
      console.error("Error accessing media devices:", err);
  
      // Handle errors (permissions, device access issues)
      if (err.name === "NotAllowedError") {
        alert("Please allow webcam and microphone access for recording.");
      } else if (err.name === "NotFoundError") {
        alert("No webcam or microphone devices were found.");
      } else {
        alert("Error accessing media devices: " + err.message);
      }
    }
  }
  