<!doctype html>
<html lang="en">
<head>
    <title>AGFL - VMS login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{url("/public/assets/css/login.css")}}">

</head>
<body>
<section class="ftco-section">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="login-wrap p-4 p-md-12">
                    <div class="d-flex">
                        <div class="w-100 text-center">
                            <h3 class="mb-12"> <img height="150" src="https://asaanghar.com/wp-content/uploads/2021/05/logo-with-urdu-tagline-png-1.png">
                            </h3>
                        </div>


                    </div>
                    <form method="post" action="{{ route('login') }}" class="login-form"> @csrf
                        <div class="form-group">
                            <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-user"></span></div>
                            <input type="text" class="form-control rounded-left" name="identity" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <div class="icon d-flex align-items-center justify-content-center"><span class="fa fa-lock"></span></div>
                            <input type="password" name="password" class="form-control rounded-left" placeholder="Password" required>

                            <br>
                            @if ($errors->any())
                                <div class="alert alert-danger alert-styled-left alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <span class="font-weight-semibold">Oops!</span> {{ implode('<br>', $errors->all()) }}
                                </div>
                            @endif

                        </div>
                        <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary rounded submit">Login</button>
                        </div>
                        <div class="form-group mt-4">
                            <div class="w-100 text-center">
                                <p class="mb-1">FINANCE MANAGEMENT SYSTEM </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



</body>
</html>

