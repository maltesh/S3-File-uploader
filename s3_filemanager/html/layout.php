<?php

/*
 * GPL License
 * UI Taken from -http://yuilibrary.com/yui/docs/uploader/uploader-dd.html
 */
?>
<html>
	<head>
            <title>
				S3 File Manager
		    </title>
           <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
           <link rel="shortcut icon" type="image/ico" href="http://airpush.com/images/airpush-fevicon.png" />
           <link rel="stylesheet" type="text/css" href="css/style.css" />

           <link rel="stylesheet" type="text/css" href="css/main-min.css" />
           <link rel="stylesheet" type="text/css" href="css/cssgrids-min.css" />
           <link rel="stylesheet" type="text/css" href="css/docs-min.css" />
           <script>
                var YUI_config={"filter":"min","maxURLLength":1024,"root":"3.6.0/","groups":{"site":{"combine":true,"comboBase":"/combo/js?","root":"","modules":{"hoverable":{"path":"hoverable-min.js","requires":["event-hover","node-base","node-event-delegate"]},"search":{"path":"search-min.js","requires":["autocomplete","autocomplete-highlighters","node-pluginhost"]},"api-filter":{"path":"apidocs/api-filter-min.js","requires":["autocomplete-base","autocomplete-highlighters","autocomplete-sources"]},"api-list":{"path":"apidocs/api-list-min.js","requires":["api-filter","api-search","event-key","node-focusmanager","tabview"]},"api-search":{"path":"apidocs/api-search-min.js","requires":["autocomplete-base","autocomplete-highlighters","autocomplete-sources","escape"]}}}}};
           </script>
           <script src="http://yui.yahooapis.com/3.6.0/build/yui/yui-min.js"></script>


    </head>
    <body>
        <div class='example'>
        <div id="exampleContainer">
        <div id="uploaderContainer">
            <div id="selectFilesButtonContainer">
            </div>
            <div id="uploadFilesButtonContainer">
            <button type="button" id="uploadFilesButton"  class="yui3-button" style="width:140px; height:30px;">Upload Files</button>
            </div>
            <div id="overallProgress" >
            </div>
        </div>

        <div id="filelist">
        <table id="filenames" style="width:60%;font:13px solid;font-family: Arial,Helvtica,Verdana,Sans-serif;border:0px solid grey;cellspacing:2;border-collapse:collpase;">
            <thead>
            <tr><th>File name</th><th>File size</th><th>Percent uploaded</th><th>File Link</th></tr>
            <tr id="nofiles">
                <td colspan="4" id="ddmessage">
                    <strong>No files selected.</strong>
                </td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
            <div style='float:right;padding-bottom:4px; '><span>Max file size is 50 mb</span></div>
        </div>

        <script>

        YUI({filter:"raw"}).use("uploader", function(Y) {
        Y.one("#overallProgress").set("text", "Uploader type: " + Y.Uploader.TYPE);
        if (Y.Uploader.TYPE != "none" && !Y.UA.ios) {
            var uploader = new Y.Uploader({width: "140px",
                                            height: "30px",
                                            multipleFiles: true,
                                            swfURL: "http://yui.yahooapis.com/3.6.0/build/uploader/assets/flashuploader.swf?t=" + Math.random(),
                                            uploadURL: "process.php",
                                            simLimit: 2,
                                            withCredentials: false
                                            ///<!--                                            maxFileSize:2097152-->
                                            });
            var uploadDone = false;

            if (Y.Uploader.TYPE == "html5") {
                uploader.set("dragAndDropArea", "body");

                Y.one("#ddmessage").setHTML("<strong>Drag and drop files here.</strong>");

                uploader.on(["dragenter", "dragover"], function (event) {
                    var ddmessage = Y.one("#ddmessage");
                    if (ddmessage) {
                        ddmessage.setHTML("<strong>Files detected, drop them here!</strong>");
                        ddmessage.addClass("yellowBackground");
                    }
                });

                uploader.on(["dragleave", "drop"], function (event) {
                    var ddmessage = Y.one("#ddmessage");
                    if (ddmessage) {
                        ddmessage.setHTML("<strong>Drag and drop files here.</strong>");
                        ddmessage.removeClass("yellowBackground");
                    }
                });
            }

            uploader.render("#selectFilesButtonContainer");

            uploader.after("fileselect", function (event) {
                var fileList = event.fileList;
                var fileTable = Y.one("#filenames tbody");
                if (fileList.length > 0 && Y.one("#nofiles")) {
                    Y.one("#nofiles").remove();
                }

                if (uploadDone) {
                    uploadDone = false;
                    fileTable.setHTML("");
                }

                var perFileVars = {};

                Y.each(fileList, function (fileInstance) {
                    var num = fileInstance.get("size")/1024;
                    var mb_num = num/1024;
                    if(mb_num >= 50){
                        alert('File max size exceeded');
                        window.location.reload();
                    }
                    fileTable.append("<tr id='" + fileInstance.get("id") + "_row" + "'>" +
                                            "<td class='filename'>" + fileInstance.get("name") + "</td>" +
                                            "<td class='filesize'>" + num.toPrecision(6) +' KB' + "</td>" +
                                            "<td class='percentdone'>Hasn't started yet</td>" +
                                            "<td class='serverdata'>&nbsp;</td>");

                    perFileVars[fileInstance.get("id")] = {filename: fileInstance.get("name")};
                                    });

                    uploader.set("postVarsPerFile", Y.merge(uploader.get("postVarsPerFile"), perFileVars));
            });

            uploader.on("uploadprogress", function (event) {
                    var fileRow = Y.one("#" + event.file.get("id") + "_row");
                        fileRow.one(".percentdone").set("text", event.percentLoaded + "%");
            });

            uploader.on("uploadstart", function (event) {
                    uploader.set("enabled", false);
                    Y.one("#uploadFilesButton").addClass("yui3-button-disabled");
                    Y.one("#uploadFilesButton").detach("click");
            });

            uploader.on("uploadcomplete", function (event) {
                    var fileRow = Y.one("#" + event.file.get("id") + "_row");
                        fileRow.one(".percentdone").set("text", "Finished!");
                        fileRow.one(".serverdata").setHTML('<a target="_blank" href="'+event.data+'">'+event.data+'</a>');
            });

            uploader.on("totaluploadprogress", function (event) {
                        Y.one("#overallProgress").setHTML("Total uploaded: <strong>" + event.percentLoaded + "%" + "</strong>");
            });

            uploader.on("alluploadscomplete", function (event) {
                            uploader.set("enabled", true);
                            uploader.set("fileList", []);
                            Y.one("#uploadFilesButton").removeClass("yui3-button-disabled");
                            Y.one("#uploadFilesButton").on("click", function () {
                                if (!uploadDone && uploader.get("fileList").length > 0) {
                                    uploader.uploadAll();
                                }
                            });
                            Y.one("#overallProgress").set("text", "Uploads complete!");
                            uploadDone = true;
            });

            Y.one("#uploadFilesButton").on("click", function () {
                if (!uploadDone && uploader.get("fileList").length > 0) {
                    uploader.uploadAll();
                }
            });
        }
        else {
            Y.one("#uploaderContainer").set("text", "We are sorry, but to use the uploader, you either need a browser that support HTML5 or have the Flash player installed on your computer.");
        }


        });
            </script>

        </div>
    </body>
</html>