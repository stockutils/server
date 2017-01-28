/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class ServerConfigController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = {processors: [], tabs: {}};
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'server').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.config = angular.isObject($scope.settings.config) ? $scope.settings.config : {image: {}};
        }

        save = () => {
            this.$scope.config.save(this.gettext('Server saved successfully'));
        };
    }

    angular.module('serverConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('serverConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', ServerConfigController]);
}
