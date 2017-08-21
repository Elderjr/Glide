var app = angular.module('myApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});

app.filter('itemParticipants', function () {
    return function (allUsers) {
        var integrantsChecked = [];
        for (var i = 0; i < allUsers.length; i++) {
            if (allUsers[i].itemParticipant && allUsers[i].billParticipant) {
                integrantsChecked.push(allUsers[i]);
            }
        }
        return integrantsChecked;
    };
});
app.controller('myCtrl', ['$scope', 'itemParticipantsFilter', '$http', 'bill', 'myGroups', 'USER_REST',
    function ($scope, itemParticipantsFilter, $http, bill, myGroups, userRest) {
        if(bill == null){
            $scope.bill = {
              id: -1,
              members: [],
              items: [],
              group: null
            };
        }else{
            $scope.bill = bill;
            for(var i = 0; i < $scope.bill.members.length; i++){
                $scope.bill.members[i].billParticipant = true;
                $scope.bill.members[i].itemParticipant = true;
                $scope.bill.members[i].contributor = $scope.bill.members[i].contribution > 0;
            }
        }
        if(myGroups == null){
            $scope.myGroups = [];
        }else{
            $scope.myGroups = myGroups;
        }
        $scope.userRest = userRest;
        $scope.totalItems = 0.0;
        $scope.step = 1;
        
        $scope.searchUser = function () {
            if ($scope.username != "") {
                $scope.loadMsg = "Procurando usuario...";
                $http.get($scope.userRest + $scope.username).then(addIntegrant);
            }
        }
        
        function existsAtLeastOneBillMember() {
            for (var i = 0; i < $scope.bill.members.length; i++) {
                if ($scope.bill.members[i].billParticipant) {
                    return true;
                }
            }
            return false;
        }

        function showMsgError(msgError) {
            new PNotify({
                title: 'Notificaçao de erro',
                text: msgError,
                type: 'error'
            });
        }
        $scope.validateFirstStep = function () {
            var msgError = null;
            if ($scope.bill.name == null) {
                msgError = "Nome da despesa deve ser preenchido";
            } else if ($scope.bill.group == null) {
                msgError = "Selecione um grupo para a despesa";
            } else if (!existsAtLeastOneBillMember()) {
                msgError = "Selecione pelo menos um integrante da despesa";
            }
            if (msgError != null) {
                showMsgError(msgError)
                return false;
            }
            $scope.step = 2;
            return true;
        }

        $scope.validateSecondStep = function () {
            var msgError = null;
            if ($scope.bill.items.length == 0) {
                msgError = 'registre pelo menos um item';
            } else {
                $scope.totalItems = 0.0;
                for (var i = 0; i < $scope.bill.items.length && msgError == null; i++) {
                    if ($scope.bill.items[i].name == "") {
                        msgError = 'O item ' + (i + 1) + 'esta sem nome';
                    } else if (!isNumber($scope.bill.items[i].price)) {
                        msgError = 'O preço do item ' + $scope.bill.items[i].name + " esta com formato errado";
                    } else if (!isNumber($scope.bill.items[i].qt)) {
                        msgError = 'A quantidade do item ' + $scope.bill.items[i].name + " esta com formato errado";
                    } else if (!$scope.checkDistribution($scope.bill.items[i])) {
                        msgError = 'O item ' + $scope.bill.items[i].name + " esta com distribuiçao errada";
                    }
                    var total = Decimal.mul($scope.bill.items[i].price, $scope.bill.items[i].qt).toNumber();
                    $scope.totalItems = Decimal.add($scope.totalItems, total).toNumber();
                }
            }
            if (msgError != null) {
                showMsgError(msgError);
                return false;
            }
            calculeValuePerMember();
            $scope.step = 3;
            return true;
        }

        $scope.validateThirdStep = function () {
            var msgError = null;
            var total = 0.0;
            var existAtLeastOneContributor;
            for (var i = 0; i < $scope.bill.members.length && msgError == null; i++) {
                if ($scope.bill.members[i].contributor) {
                    existAtLeastOneContributor = true;
                }
                if (!isNumber($scope.bill.members[i].contribution)) {
                    msgError = 'Contribuiçao do usuario ' + $scope.bill.members[i].user.name + " esta no formato errado";
                }
                total = Decimal.add(total, $scope.bill.members[i].contribution).toNumber();
            }
            if (msgError != null) {
                showMsgError(msgError);
                return false;
            } else if (!existAtLeastOneContributor) {
                showMsgError('Registre pelo menos um contribuidor');
                return false;
            } else if (total != $scope.totalItems) {
                showMsgError('Total de contribuiçao diferente do total da despesa');
                return false;
            } else {
                return true;
            }
        }

        function addIntegrant(response) {
            if (response.data != "null") {
                var user = response.data;
                addMember(user, 0.0);
                $scope.loadMsg = "";
                $scope.username = "";
            } else {
                $scope.loadMsg = "Usuario nao encontrado";
            }
        }


        function addMember(user) {
            if (!existMember(user.id)) {
                var member = {
                    id: -1,
                    user: user,
                    contribution: 0.0,
                    paid: 0.0,
                    value: 0.0,
                    itemParticipant: true,
                    billParticipant: true,
                    contributor: false
                };
                $scope.bill.members.push(member);
            }
        }

        $scope.setStep = function (step) {
            $scope.step = step;
        }

        $scope.addContributor = function () {
            if ($scope.rContributor.member != null && $scope.rContributor.value != null) {
                var member = getMemberById($scope.rContributor.member.user.id);
                member.contribution = $scope.rContributor.value;
                member.contributor = true;
            }
        }

        $scope.removeContributor = function (member) {
            member.contributor = false;
            member.contribution = 0.0;
        }

        $scope.onGroupSelected = function () {
            if ($scope.groupSelected != null) {
                $scope.bill.members = [];
                for (var i = 0; i < $scope.groupSelected.members.length; i++) {
                    addMember($scope.groupSelected.members[i].user);
                    $scope.bill.group = {id: $scope.groupSelected.id,
                        name: $scope.groupSelected.name
                    };
                }
            }
        }


        function getMemberById(userId) {
            for (var i = 0; i < $scope.bill.members.length; i++) {
                if ($scope.bill.members[i].user.id == userId) {
                    return $scope.bill.members[i];
                }
            }
            return null;

        }

        function existMember(userId) {
            return (getMemberById(userId) != null);
        }

        function clearItem() {
            $scope.rItem.name = "";
            $scope.rItem.price = 0.0;
            $scope.rItem.qt = 1;
        }

        $scope.addItem = function () {
            var itemMembers = itemParticipantsFilter($scope.bill.members);
            var item = {
                id: -1,
                name: $scope.rItem.name,
                price: $scope.rItem.price,
                qt: $scope.rItem.qt,
                members: []
            };
            if (itemMembers.length > 0) {
                var dist = makeDistribution(Decimal.mul(item.price, item.qt), itemMembers.length);
                for (var i = 0; i < itemMembers.length; i++) {
                    var member = {
                        id: -1,
                        user: itemMembers[i].user,
                        distribution: dist[i]};
                    item.members.push(member);
                }
            }
            clearItem();
            $scope.bill.items.push(item);
        }

        function addElement(element, array) {
            var index = array.indexOf(element);
            if (index == -1) {
                array.push(element);
            }
        }

        $scope.removeElement = function (element, array) {
            var index = array.indexOf(element);
            if (index >= 0) {
                array.splice(index, 1);
            }
        }

        function makeDistribution(price, n) {
            var dist = [];
            var initialDist = Decimal.div(price, n).floor(2).toNumber();
            for (var i = 0; i < n; i++) {
                dist[i] = initialDist;
            }
            var rest = Decimal.sub(price, Decimal.mul(initialDist, n)).toNumber();
            var index = 0;
            while (rest > 0) {
                dist[index] = Decimal.add(dist[index], 0.01).toNumber();
                rest = Decimal.sub(rest, 0.01).toNumber();
                index = (index + 1) % dist.length;
            }
            return dist;
        }
        $scope.checkDistribution = function checkDistribution(item) {
            if (isNumber(item.price) && isNumber(item.qt)) {
                var total = Decimal.mul(item.price, item.qt).toNumber();
                var sum = 0.0;
                for (var i = 0; i < item.members.length; i++) {
                    if (!isNumber(item.members[i].distribution)) {
                        return false;
                    }
                    sum = Decimal.add(sum, item.members[i].distribution).toNumber();
                }
                return sum == total;
            }
            return false;
        }


        function calculeValuePerMember() {
            //reset values
            for (var i = 0; i < $scope.bill.members.length; i++) {
                $scope.bill.members[i].value = 0.0;
            }
            for (var i = 0; i < $scope.bill.items.length; i++) {
                for (var j = 0; j < $scope.bill.items[i].members.length; j++) {
                    var member = getMemberById($scope.bill.items[i].members[j].user.id);
                    if (isNumber($scope.bill.items[i].members[j].distribution)) {
                        member.value = Decimal.add(member.value, $scope.bill.items[i].members[j].distribution);
                    }
                }
            }
        }

        function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
    }]);

function validateBillForm() {
    return angular.element($("#billForm")).scope().validateThirdStep();
}