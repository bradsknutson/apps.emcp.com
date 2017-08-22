<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>Audio Album Generator</title>
        <link rel="stylesheet" href="http://apps.emcp.com/editor/css/bootstrap.min.css">
        <link rel="stylesheet" href="http://apps.emcp.com/editor/css/style.css">
        <link rel="stylesheet" href="http://apps.emcp.com/redirects/lib/css/style.css">
        <link rel="stylesheet" href="http://apps.emcp.com/redirects/lib/css/font-awesome.css">
        <link rel="stylesheet" href="lib/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="http://apps.emcp.com/editor/js/bootstrap.min.js"></script>
        <script src="http://apps.emcp.com/redirects/lib/js/script.js"></script>
        <script src="lib/js/script.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h2>Audio Album Generator</h2>
                        <p><a href="http://resources.emcp.com/ebooks/audio-albums/example-album/" target="_blank">Click here</a> to see an example.  All albums generated will follow this template.</p>
                        <p><a href="http://apps.emcp.com/build/audio-album/directory/" target="_blank">Click here</a> to get a list of previously generated Audio Albums.</p>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="step-1">

                            <div class="dialog-label">Step 1. Enter the album Name and eBook Folder Name.</div>
                            <form class="form-step-1 dialog-content">
                                <div class="form-group">
                                    <input type="text" name="title" class="form-control" id="title" placeholder="Album name (example: MLT Monos)" autocomplete="off">
                                </div>
                                <div class="form-group margin-top">
                                    <input type="text" name="ebook" class="form-control" id="ebook" placeholder="eBook Folder name (example: MLT_E_5_Monos)" autocomplete="off">
                                </div>
                                <div class="dialog-options">
                                    <div class="button-submit">Submit</div>
                                </div>
                                <button type="submit" class="btn btn-default hidden">Submit</button>
                            </form>
                        </div>
                        
                        <div class="step-2">
                            
                            <div class="dialog-label">Step 2. Upload the Album Art or Book Cover</div>

                            <form class="form-step-2 dialog-content" enctype="multipart/form-data" method="POST">
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="hidden" name="MAX_FILE_SIZE" value="1000000"><input name="albumArt" type="file" style="display: none;">
                                        </span>
                                    </label>
                                    <input type="hidden" name="albumDirectory" value="">
                                    <input type="text" class="form-control" readonly="">
                                </div>
                                <div class="dialog-options">
                                    <div class="button-submit">Upload</div>
                                </div>
                                <div class="form-group hidden">
                                    <button type="submit" class="btn btn-default">Upload</button>
                                </div>
                            </form>

                            <div class="display-album-art">
                                <div class="dialog-label">Preview of album art</div>
                                <div class="dialog-content display-album-art-container"></div>
                                <div class="dialog-options">
                                    <div class="button-accept">Accept</div>
                                    <div class="button-trash">Trash</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="step-3">

                            <div class="dialog-label">Step 3. Upload the Audio List (.csv)</div>

                            <form class="form-step-3 dialog-content" enctype="multipart/form-data" method="POST">
                                <p>Generate your own file using <a href="https://docs.google.com/a/emcp.com/spreadsheets/d/1ZVVu9Nuby1okJcMT_DfS9kajFj43dCYSDENVy59UXhs/edit?usp=sharing" target="_blank" data-content="To view the template, make sure you are logged into your @emcp.com Google Drive account." data-toggle="popover" data-placement="top" data-trigger="hover" data-original-title="Can't view the file?">the template <i class="fa fa-info-circle" aria-hidden="true"></i></a>. Open the template, click File > Make a Copy, and fill out the two columns with the File Names and Track Titles. When finished, click <span class="modal-click-style">File > Download As > Comma-separated values (.csv)</span>.</p>

                                <p>Here is a properly filled out <a href="https://docs.google.com/a/emcp.com/spreadsheets/d/1YnqTdIsU_nXmR9Swy5pnoCUvAf6BqRLhHwApqGqpKC4/edit?usp=sharing" target="_blank" data-content="To view the example file, make sure you are logged into your @emcp.com Google Drive account." data-toggle="popover" data-placement="top" data-trigger="hover" data-original-title="Can't view the file?">example file <i class="fa fa-info-circle" aria-hidden="true"></i></a>.</p>
                                
                                <div class="input-group">
                                    <label class="input-group-btn">
                                        <span class="btn btn-primary">
                                            Browse&hellip; <input type="hidden" name="MAX_FILE_SIZE" value="500000"><input name="audioFileList" type="file" style="display: none;">
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" readonly="">
                                </div>
                                
                                <div class="dialog-options">
                                    <div class="button-submit">Upload</div>
                                </div>
                                <div class="form-group hidden">
                                    <button type="submit" class="btn btn-default">Upload</button>
                                </div>
                            </form>

                            <div class="display-audio-file-list">
                                <div class="dialog-label">Preview of audio file info</div>
                                <div class="dialog-content">
                                    <p>Take this time to make any modifications to the audio file sort order (numerically sorted by the values in the first column), the File Name (no spaces or special characters), and the File Title.</p>
                                </div>
                                <div class="dialog-content display-audio-file-list-container"></div>
                                <div class="dialog-options">
                                    <div class="button-complete">Finish</div>
                                    <div class="button-trash">Clear</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="step-4">
                            
                        </div>
                        
                    </div>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
        <div class="loader-container">
            <div class="loader">
                <div><span>{</span>loading<span>}</span></div>
            </div>
        </div>
        <div class="hidden step-1-return"></div>
        <div class="hidden step-2-return"></div>
        <div class="hidden step-3-return"></div>
        <div class="hidden step-4-return"></div>
        <div class="hidden album-name"></div>
        <div class="hidden ebook-slug"></div>
        <div class="hidden directory-name"></div>
        <div class="hidden image-name"></div>
        
        <div class="file-download-info-modal">
            <div class="modal fade" id="fileDownloadAsModal" tabindex="-1" role="dialog" aria-labelledby="fileDownloadAsModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="material-design-modal">
                        <div class="dialog-label">
                            Click File > Download As > Comma-separated values (.csv)
                        </div>
                        <div class="dialog-content">
                            <img src="lib/img/download-as.png" />
                        </div>
                        <div class="dialog-options">
                            <div class="button-close" data-dismiss="modal" >Close</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
</html>
