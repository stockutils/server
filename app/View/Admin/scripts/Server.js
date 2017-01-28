/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var ServerConfigController = (function () {
        function ServerConfigController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.save = function () {
                _this.$scope.config.save(_this.gettext('Server saved successfully'));
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = { processors: [], tabs: {} };
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'server').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.config = angular.isObject($scope.settings.config) ? $scope.settings.config : { image: {} };
        }
        return ServerConfigController;
    }());
    Admin.ServerConfigController = ServerConfigController;
    angular.module('serverConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('serverConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', ServerConfigController]);
})(Admin || (Admin = {}));
