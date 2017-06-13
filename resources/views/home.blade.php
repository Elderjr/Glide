<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Stylish Portfolio - Start Bootstrap Theme</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/stylish-portfolio.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">        

        <!-- Angular Js -->
        <script src="js/angular.min.js"></script>
        <script>
            var app = angular.module("homeApp", []);
            app.controller("homeController", function ($scope) {
                $scope.login = true;

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

        <header id="top" class="header container" ng-app="homeApp" ng-controller="homeController" >
            <div class="text-vertical-center">
                <h1>Glide</h1>
                <h4>Gerenciador de Despesas</h4>
                <!-- Divs Alertas
                <div class="alert alert-success">
                    <strong>Conta criada com sucesso!</strong>
                </div>
                <div class="alert alert-danger">
                    <strong>Login ou senha incorreto(s)</strong>
                </div>
                -->
                <div class="home-container" ng-if="login">
                    <form>
                        <div class="form-group">
                            <label for="email">Username:</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                            <label for="pwd">Senha:</label>
                            <input type="password" class="form-control" id="pwd">
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox">Lembrar de mim</label>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Entrar</button>
                        <div class="or-span">
                            <span> ou </span>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block" ng-click="goToRegister()">Cadastre-se</button>
                    </form>
                </div>
                <div class="home-container" ng-if="!login">
                    <div class="form-group">
                        <form>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="email">Nome:</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Username:</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-12">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label for="email">Senha:</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Confirmar senha:</label>
                                    <input type="email" class="form-control" id="email">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Cadastrar</button>
                            <div class="or-span">
                                <span> ou </span>
                            </div>
                            <button type="submit" class="btn btn-default btn-block" ng-click="goToLogin()">Voltar para Login</button>
                        </form> 
                    </div>
                </div>

            </div>

        </header>

    </body>

</html>