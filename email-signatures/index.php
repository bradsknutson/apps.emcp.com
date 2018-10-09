<?php
        
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>EMC/Paradigm/JIST Email Signatures</title>
        <link rel="stylesheet" href="http://media.emcp.com/jist-redirects/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="http://media.emcp.com/jist-redirects/js/bootstrap.min.js"></script>
        <script src="js/script.js"></script>
    </head>
    <body>
        <div class="row">
            <div class="col-xs-1 col-md-3"></div>
            <div class="col-xs-10 col-md-6">
                <h1>Email Signature Generator</h1>
                
                <div class="section-1">
                    <h2>Step 1. Fill Out The Form Below</h2>

                    <form class="signature">
                        <div class="form-group">
                            <label for="salutation">Closing Phrase (Optional)</label>
                            <input type="text" value="" class="form-control" id="salutation" placeholder="Closing Phrase" data-toggle="tooltip" data-placement="top" title="Example: Thanks or Regards" >
                        </div>
                        <div class="form-group">
                            <label for="name">Name<span class="red">*</span></label>
                            <input type="text" required="required" value="" class="form-control" id="name" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="name">Title<span class="red">*</span></label>
                            <input type="text" required="required" value="" class="form-control" id="title" placeholder="Title">
                        </div>
                        <div class="form-group">
                            <label for="email">Email<span class="red">*</span></label>
                            <input type="email" required="required" value="" class="form-control" id="email" placeholder="email@emcp.com">
                        </div>
                        <div class="form-group">
                            <label for="name">Phone<span class="red">*</span></label>
                            <input type="phone" required="required" value="" class="form-control" id="phone" placeholder="Phone" data-toggle="tooltip" data-placement="top" title="With or Without Extension" >
                        </div>
                        <p><strong>Include 2nd Phone Number?</strong></p>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" id="2ndphone">
                            </span>
                            <input type="text" class="form-control" id="800num" placeholder="2nd Phone (Ex: 800 Number)" disabled >
                        </div><br />
                        <p><strong>What Division Do You Represent?<span class="red">*</span></strong></p>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="division" id="division-emc" class="uncheck" value="EMC School">
                                EMC School
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="division" id="division-pes" class="uncheck" value="Paradigm Education Solutions">
                                Paradigm Education Solutions
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="division" id="division-jist" class="uncheck" value="JIST Career Solutions">
                                JIST Career Solutions
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="division" id="division-all" value="EMC Publishing">
                                Internal - Represent Entire Entity
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

                <div class="section-2">
                    <h2>Step 2. Copy The Generated Email Signature Below</h2>
                    <button class="btn btn-primary edit">Edit Signature</button>
                </div>   

                <table class="table table-bordered">
                    <tr>
                        <td class="padding"  id="selectme">
                            <p style="font-family:Calibri,Arial,sans-serif;font-size:14px;line-height:1.2;margin:0 0 10px;color:#000000;"><span class="sig-salutation">,<br /><br /></span>
                            <span class="sig-name"></span> | <span class="sig-title"></span><br />
                            <span class="sig-division"></span><br />
                            <span class="sig-phone"></span><br />
                            <span class="sig-800"></span>
                            <span class="sig-email"></span></a></p>
                            <table>
                                <tr>
                                    <td class="emc-logo">
                                        <a href="http://www.emcp.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/emc.png" alt="EMC School" height="35" /></a>&nbsp;&nbsp;
										<a href="http://www.emcp.com/zulama" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/zulama.png" alt="EMC School - Zulama" /></a>
                                    </td>
                                    <td class="paradigm-logo">
                                        <a href="http://paradigmcollege.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/paradigm.png" alt="Paradigm Education Solutions" height="35" />
                                    </td>
                                    <td class="jist-logo">
                                        <a href="http://jist.com" target="_blank"><img src="http://apps.emcp.com/email-signatures/img/jist.png" alt="JIST Career Solutions" height="35" />
                                    </td>
                                </tr>
								<tr>
									<td colspan="3" class="emc-logo">
										<a href="https://www.facebook.com/EMCPublishing"><img src="http://apps.emcp.com/email-signatures/img/facebook.png" width="35" alt="EMC School - Facebook" /></a>&nbsp;
										<a href="https://twitter.com/EMCschool"><img src="http://apps.emcp.com/email-signatures/img/twitter.png" width="37" alt="EMC School - Twitter" /></a>&nbsp;
										<a href="https://vimeo.com/emcschool"><img src="http://apps.emcp.com/email-signatures/img/vimeo.png" width="37" alt="EMC School - Vimeo" /></a>
									</td>
								</tr>
                            </table>
                        </td>
                    </tr>
                </table>
                    
                <div class="section-3">
                    <h2>Step 3. Create Signature in Outlook</h2>
                    <blockquote>
                        <h3>Windows Users</h3>
                        <ol>
                            <li>Open a new message. On the <strong>Message</strong> tab, in the <strong>Include</strong> group, click <strong>Signature</strong>, and then click <strong>Signatures</strong>.<br /><br /><img src="https://support.content.office.net/en-us/media/9197603e-dbe6-4b6f-9939-7341fb07cf79.jpg" /><br /><br /></li>
                            <li>On the <strong>E-Mail Signature</strong> tab, click <strong>New</strong>.</li>
                            <li>Type a name for the signature, and then click <strong>OK</strong>.</li>
                            <li>In the <strong>Edit signature</strong> box, paste in the signature that you copied from the generator above.</li>
                            <li>Under <strong>Choose default signature</strong>, in the <strong>New messages</strong> list, select the signature that you want to include.</li>
                            <li>If you want a signature to be included when you reply to or forward messages, in the <strong>Replies/forwards</strong> list, select the signature. Otherwise, click (<strong>none</strong>).</li>
                        </ol>
                        <h3>Mac Users</h3>
                        <ol>
                            <li>Open a new message. Click <strong>Signatures</strong>, then click <strong>Edit Signatures...</strong></li>
                            <li>Click the <strong>+</strong> icon to add a new signature. Doule-click <strong>Untitled</strong>, and rename your signature.</li>
                            <li>In the <strong>Signature</strong> box, remove any existing content, and paste in the signature that you copied from the generator above.</li>
                            <li>Under <strong>Choose default signature</strong>, in the <strong>New messages</strong> list, select the signature that you want to include.</li>
                            <li>If you want a signature to be included when you reply to or forward messages, in the <strong>Replies/forwards</strong> list, select the signature. Otherwise, click (<strong>none</strong>).</li>
                        </ol>
                    </blockquote>                
                </div> 
                
            
            </div>
            <div class="col-xs-1 col-md-3"></div>
        </div>
    </body>
</html>