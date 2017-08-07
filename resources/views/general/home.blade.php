<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Glide - Login</title>

        <!-- Bootstrap Core CSS -->
        <link href="custom/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="custom/login/login.css" rel="stylesheet">

        <!-- Angular Js -->
        <script src="js/angular.min.js"></script>
        <script>
            var app = angular.module('homeApp', [], function ($interpolateProvider) {
                $interpolateProvider.startSymbol('<%');
                $interpolateProvider.endSymbol('%>');
            });
            app.controller("homeController", function ($scope) {
                $scope.login = true;
                        @if (session('errors'))
                        $scope.login = false;
                        @endif
                        $scope.goToRegister = function () {
                            $scope.login = false;
                        }

                $scope.goToLogin = function () {
                    $scope.login = true;
                }
            });
        </script>
    </head>

    <body>

        <header id="top" class="header container-fluid" ng-app="homeApp" ng-controller="homeController" >
            <div class="title">
                <h1>Glide</h1>
                <h4>Gerenciador de Despesas</h4>
                @if(session('errors'))
                <div class="alert alert-danger">
                    Os seguintes erros foram encontrados:
                    <ul>
                        @foreach(session('errors') as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @elseif(session('login_fail'))
                <div class="alert alert-danger">
                    Login ou senha incorreto(s)
                </div>
                @elseif(session('register_success'))
                <div class="alert alert-success">
                    Cadastro feito com sucesso
                </div>
                @endif
            </div>
            <div class="form-container">

                <div class="form-box" ng-if="login">
                    <form action="{{action('AuthenticationController@authenticate')}}" method='post'>
                        {{ csrf_field()}}
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name='username' required>
                        </div>
                        <div class="form-group">
                            <label for="pwd">Senha:</label>
                            <input type="password" class="form-control" id="pwd" name='password' required>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox">Lembrar de mim</label>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Entrar</button>
                        <div class="or-span">
                            <span> ou </span>
                        </div>
                        <button type="button" class="btn btn-warning btn-block" ng-click="goToRegister()">Cadastre-se</button>
                    </form>
                </div>
                <div class="form-box" ng-if="!login">
                    <div class="form-group">
                        <form action="{{action('UserController@store')}}" method="post">
                            {{ csrf_field()}}
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="name">Nome:</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="password">Senha:</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmPassword">Confirmar senha:</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Cadastrar</button>
                            <div class="or-span">
                                <span> ou </span>
                            </div>
                            <button type="button" class="btn btn-default btn-block" ng-click="goToLogin()">Voltar para Login</button>
                        </form> 
                    </div>
                </div>
            </div>
        </header>

    </body>

</html>