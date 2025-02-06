<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    // Function to warn user before attempting to cheat
    // Swal fire on page load

    Swal.fire({
        title: 'Important!',
        html: "You have <b>15 seconds</b> to read the instructions.<br><br>Note: This exam has anti-cheating features such as tab-switch detection, screen resizing restrictions, and click monitoring outside the form. Any suspicious activity will result in automatic submission of your exam.<br><br><b>Please select the entire screen for recording and allow microphone and camera permissions.</b>",
        icon: 'warning',
        allowOutsideClick: false,
        confirmButtonText: 'Start Exam',
        timer: 5000, // 15 seconds timer
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        },
        willClose: () => {

            startScreenAndWebcamRecording();
            //antycheat programs
            // Start the exam

            setTimeout(() => {
                monitorTabSwitch();
                monitorWindowResize();
                monitorWindowMinimize();
                monitorClickOutside();
            }, 10000); // Set according to your quiz time or auto-stop logic
        }
    });

    function preventBack() { window.history.forward(); }
    setTimeout("preventBack()", 0);
    window.onunload = function () { null };



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

            $(document).on('submit', '#submitAnswerFrm', function () {
                setTimeout(() => {
                    screenRecorder.stop();
                    webcamRecorder.stop();

                }, 3000);

            });



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
    //startScreenAndWebcamRecording();








    // Monitor tab switching
    let tabSwitchDetected = false;
    function monitorTabSwitch() {
        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                if (!tabSwitchDetected) {
                    tabSwitchDetected = true;

                    // Automatically submit the exam form
                    document.getElementById('submitAnswerFrmBtn').click();


                }
            }
        });
    }

    // Monitor window resizing
    function monitorWindowResize() {
        window.addEventListener("resize", function () {


            // Automatically submit the exam form
            document.getElementById('submitAnswerFrmBtn').click();



        });
    }

    // Monitor window minimizing
    function monitorWindowMinimize() {
        window.addEventListener("blur", function () {

            // Automatically submit the exam form
            document.getElementById('submitAnswerFrmBtn').click();

        });
    }

    // Monitor clicks outside the exam form
    function monitorClickOutside() {
        document.body.addEventListener("click", function (e) {
            const examForm = document.getElementById('submitAnswerFrm');
            if (examForm && !examForm.contains(e.target)) {

                // Automatically submit the exam form
                document.getElementById('submitAnswerFrmBtn').click();

            }
        });
    }

//     // Intercept reload, close, or navigate away
// window.onbeforeunload = function (e) {
//     var message = "You have an active exam session. Are you sure you want to leave?";
//     e = e || window.event;

//     if (e) {
//         e.returnValue = message;
//     }

//     return message;
// };

// // Disable F5 and Ctrl+R reload
// document.addEventListener('keydown', function (event) {
//     if (event.key === 'F5' || (event.ctrlKey && event.key === 'r')) {
//         event.preventDefault();
//         alert("Reload is disabled during the exam!");
//     }
// });

// // Disable back button functionality
// history.pushState(null, null, window.location.href);
// window.onpopstate = function () {
//     history.pushState(null, null, window.location.href);
// };



</script>


<?php
$examId = $_GET['id'];
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId' ")->fetch(PDO::FETCH_ASSOC);
$selExamTimeLimit = $selExam['ex_time_limit'];
$exDisplayLimit = $selExam['ex_questlimit_display'];
?>


<div class="app-main__outer">
    <div class="app-main__inner">
        <div class="col-md-12">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div>
                            <?php echo $selExam['ex_title']; ?>
                            <div class="page-title-subheading">
                                <?php echo $selExam['ex_description']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="page-title-actions mr-5" style="font-size: 20px;">
                        <form name="cd">
                            <input type="hidden" name="" id="timeExamLimit" value="<?php echo $selExamTimeLimit; ?>">
                            <label>Remaining Time : </label>
                            <input style="border:none;background-color: transparent;color:blue;font-size: 25px;"
                                name="disp" type="text" class="clock" id="txt" value="00:00" size="5" readonly="true" />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 p-0 mb-4">
            <form method="post" id="submitAnswerFrm">
                <input type="hidden" name="exam_id" id="exam_id" value="<?php echo $examId; ?>">
                <input type="hidden" name="examAction" id="examAction">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
                    <?php
                    $selQuest = $conn->query("SELECT * FROM exam_question_tbl WHERE exam_id='$examId' ORDER BY rand() LIMIT $exDisplayLimit ");
                    if ($selQuest->rowCount() > 0) {
                        $i = 1;
                        while ($selQuestRow = $selQuest->fetch(PDO::FETCH_ASSOC)) { ?>
                            <?php $questId = $selQuestRow['eqt_id']; ?>
                            <tr>
                                <td>
                                    <p><b><?php echo $i++; ?> .) <?php echo $selQuestRow['exam_question']; ?></b></p>
                                    <div class="col-md-4 float-left">
                                        <div class="form-group pl-4 ">
                                            <input name="answer[<?php echo $questId; ?>][correct]"
                                                value="<?php echo $selQuestRow['exam_ch1']; ?>" class="form-check-input"
                                                type="radio" value="" id="invalidCheck">

                                            <label class="form-check-label" for="invalidCheck">
                                                <?php echo $selQuestRow['exam_ch1']; ?>
                                            </label>
                                        </div>

                                        <div class="form-group pl-4">
                                            <input name="answer[<?php echo $questId; ?>][correct]"
                                                value="<?php echo $selQuestRow['exam_ch2']; ?>" class="form-check-input"
                                                type="radio" value="" id="invalidCheck">

                                            <label class="form-check-label" for="invalidCheck">
                                                <?php echo $selQuestRow['exam_ch2']; ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-8 float-left">
                                        <div class="form-group pl-4">
                                            <input name="answer[<?php echo $questId; ?>][correct]"
                                                value="<?php echo $selQuestRow['exam_ch3']; ?>" class="form-check-input"
                                                type="radio" value="" id="invalidCheck">

                                            <label class="form-check-label" for="invalidCheck">
                                                <?php echo $selQuestRow['exam_ch3']; ?>
                                            </label>
                                        </div>

                                        <div class="form-group pl-4">
                                            <input name="answer[<?php echo $questId; ?>][correct]"
                                                value="<?php echo $selQuestRow['exam_ch4']; ?>" class="form-check-input"
                                                type="radio" value="" id="invalidCheck">

                                            <label class="form-check-label" for="invalidCheck">
                                                <?php echo $selQuestRow['exam_ch4']; ?>
                                            </label>
                                        </div>
                                    </div>
                </div>


                </td>
                </tr>

            <?php }
                        ?>
            <tr>
                <td style="padding: 20px;">
                    <button type="button" class="btn btn-xlg btn-warning p-3 pl-4 pr-4" id="resetExamFrm">Reset</button>
                    <input name="submit" type="submit" value="Submit"
                        class="btn btn-xlg btn-primary p-3 pl-4 pr-4 float-right" id="submitAnswerFrmBtn">
                </td>
            </tr>

            <?php
                    } else { ?>
            <b>No question at this moment</b>
        <?php }
                    ?>
        </table>

        </form>
    </div>
</div>