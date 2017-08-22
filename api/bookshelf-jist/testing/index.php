<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>EMC School | Bookshelf</title>
        <link rel="stylesheet" href="https://apps.emcp.com/editor/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://apps.emcp.com/api/bookshelf/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://apps.emcp.com/editor/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-3"></div>
                <div class="col-sm-12 col-md-6">
                    <div class="jumbotron">
                        <h1>Internal Use Only</h1>
                        <p>Use this form to test different use cases for the auto bookshelf sampling feature.</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6">
                            <div class="row">
                                <form class="generate" method="POST" action="https://apps.emcp.com/api/bookshelf/testing/process/">
                                    <div class="form-group">
                                        <label for="fname">Service:</label>
                                        <input class="form-control" id="service" name="service" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="fname">First Name:</label>
                                        <input class="form-control" id="fname" name="fname" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="lname">Last Name:</label>
                                        <input class="form-control" id="lname" name="lname" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email address:</label>
                                        <input class="form-control" id="email" name="email" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="book_id">Book ID:</label>
                                        <input class="form-control" id="book_id" name="book_id" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="duration">Duration:</label>
                                        <input class="form-control" id="duration" name="duration" value="" />
                                    </div>
                                    <input class="btn btn-primary" type="submit" style="display:none;" value="Submit">
                                    <button type="button" class="btn btn-danger btn-lg btn-block">Submit</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-3"></div>
                    </div>
                </div>                    
                <div class="col-sm-12 col-md-3"></div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('.btn-danger').click(function() {
                    $('.generate').submit();
                });
            });
        </script>
    </body>
</html>
