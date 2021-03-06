<!DOCTYPE html>
<html>
    <head>
        <?php
            $token = hash('sha256', 'LEN2M1s0d2Q8ZD9FfTptJg==');
        ?>

        <script>
            var token = '<?php echo $token ?>';
        </script>

        <link rel="stylesheet" href="assets/css/stylesheet.css" type="text/css"/>

        <title>SmartKitchen -- Making food storage easier</title>

        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-animate.js"></script>

        <script src="assets/js/moment.js"></script>
        <script src="objects/Item.js"></script>
        <script src="objects/Alert.js"></script>

        <!-- Declares the AngularJS application and application constants. -->
        <script src="assets/js/smartKitchen.js"></script>

        <!-- Load in the AngularJS application controllers. -->
        <script src="controllers/mainController.js"></script>
        <script src="controllers/navController.js"></script>
        <script src="controllers/healthController.js"></script>
        <script src="controllers/inventoryController.js"></script>

        <!-- Load in the AngularJS application services. -->
        <script src="services/logService.js"></script>
        <script src="services/refreshDataService.js"></script>
        <script src="services/cacheService.js"></script>
        <script src="services/restService.js"></script>
        <script src="services/messagesService.js"></script>
    </head>
    <body ng-app="smartKitchen" ng-controller="mainController">
        <header>
            <span class="logo" ng-bind-html="htmlMessages('SMART_KITCHEN')">
            </span>
            <nav id="top" ng-controller="navController">
                <span ng-click="showAlerts()" ng-bind-html="alerts" class="alerts"></span>
                <ul>
                    <li>
                        <a href="#">{{messages('INVENTORY')}}</a>
                        <span class="small">{{inventory}}</span>
                    </li>
                    <li>
                        <a href="#">{{messages('HELP')}}</a>
                    </li>
                    <li>
                        <a href="#">{{messages('ABOUT')}}</a>
                    </li>
                </ul>
            </nav>
            <div id="alerts" ng-show="alertsVisible" class="fade">
                <ul>
                    <li ng-repeat="alert in alertList track by $index | orderBy: '-severity'">
                        <div class="alert-delete" ng-click="removeAlert(alert)">
                            x
                        </div>
                        {{alert.message}}
                    </li>
                </ul>
            </div>
            <div id="slogan">
                <span ng-bind-html="htmlMessages('SLOGAN')"></span>
                <!--<div class="large-button">
                    Read More
                </div>-->
            </div>
        </header>
        <div ng-controller="inventoryController">
            <div id="content">
                <div id="health" class="panel" ng-controller="healthController">
                    <div class="header">
                        <span>{{messages('KITCHEN_HEALTH')}}</span>
                        <span class="loader" ng-show="healthLoading" style="margin-left: 110px; margin-top: -5px;">
                            <img src="assets/img/panelLoading.gif" />
                        </span>
                    </div>
                    <table>
                        <tr>
                            <td>{{messages('FRIDGE')}}:</td>
                            <td ng-bind-html="health.fridge">{{health.fridge}}</td>
                            <td>{{messages('NETWORK')}}:</td>
                            <td ng-bind-html="health.network">{{health.network}}</td>
                            <td>{{messages('SCANNER')}}:</td>
                            <td ng-bind-html="health.scanner">{{health.scanner}}</td>
                        </tr>
                    </table>
                    <div style="width: 125px;" class="button delete" ng-click="restartScanner()" ng-show="showReset">Reset Scanner</div>
                </div>
                <div id="latest-additions" class="panel">
                    <div class="header">
                        <span>{{messages('LATEST_INVENTORY')}}</span>
                        <span class="loader" ng-show="inventoryLoading" style="margin-left: 120px; margin-top: -5px;">
                            <img src="assets/img/panelLoading.gif" />
                        </span>
                    </div>
                    <table>
                        <tr ng-repeat="new in latest track by $index" ng-animate="'animate'">
                            <td>
                                <img ng-src="{{new.getImage()}}" alt=""/>
                            </td>
                            <td>
                                <span>
                                    <strong>{{messages('NAME')}}</strong>:
                                    {{new.getName()}}<br/>
                                    <strong>{{messages('EXPIRATION_DATE')}}</strong>:
                                    {{new.getExpires()}}
                                </span>
                            </td>
                            <td>
                                <div class="button" ng-click="togglePopup(new)">{{messages('EDIT')}}</div>
                                <div class="button delete" ng-click="deleteItem(new)">{{messages('DELETE')}}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="inventory">
                <div class="panel">
                    <div class="header">
                        <span>{{messages('INVENTORY')}}</span>
                        <span class="loader" ng-show="inventoryLoading" style="margin-left: 75px; margin-top: -5px;">
                            <img src="assets/img/panelLoading.gif" />
                        </span>
                    </div>
                    <table>
                        <tr ng-repeat="section in (inventory.length/2 | array) track by $index" ng-animate="'animate'">
                            <td ng-repeat="item in inventory.slice(2*$index, 2*$index + 2) track by $index">
                                <table>
                                    <tr>
                                        <td style="width: 20%">
                                            <img ng-src="{{item.getImage()}}" alt=""/>
                                        </td>
                                        <td style="width: 40%">
                                            <span>
                                                <strong>{{messages('NAME')}}</strong>:
                                                {{item.getName()}}<br/>
                                                <strong>{{messages('EXPIRATION_DATE')}}</strong>:
                                                {{item.getExpires()}}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="button" ng-click="togglePopup(item)">{{messages('EDIT')}}</div>
                                            <div class="button delete" ng-click="deleteItem(item)">{{messages('DELETE')}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div id="popup" ng-show="showPopup" class="fade">
            <div class="glass">
                <div class="popup">
                    <div class="header">
                    {{messages('EDIT_INVENTORY_ITEM')}}
                    </div>
                    <div class="x" ng-click="togglePopup()">
                        x
                    </div>
                    <table>
                        <tr>
                            <td>
                                <img ng-src="{{popupObject.image}}" alt=""/>
                            </td>
                            <td>
                                <span>
                                    <strong>{{messages('NAME')}}</strong>:
                                    <input style="margin-left: 34px; width: 125px" id="popupName" ng-model="popupObject.name" />
                                    <br/>
                                    <strong>{{messages('EXPIRATION_DATE')}}</strong>:
                                    <input id="popupDate" type="date" ng-model="popupObject.expiresDate" />
                                </span>
                            </td>
                        </tr>
                    </table>
                    <div class="button" ng-click="togglePopup(popupObject, true)">
                        Submit
                    </div>
                </div>
            </div>
        </div>
        <div id="busy" ng-show="busy" class="fade">
            <div class="glass"><img src="assets/img/loader.gif" /></div>
        </div>
        <footer>
            Lastest Refresh: {{latestRefresh}}<br />
            Copyright &copy; 2016 SmartKitchen. All Rights Reserved.
        </footer>

        <script>
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("popupDate").setAttribute('min', today);
        </script>
    </body>
</html>
