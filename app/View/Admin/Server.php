<div class="content-wrapper ng-cloak" ng-app="serverConfigApp" ng-controller="serverConfigController as mainCtrl" ng-init="init()">
    <div class="admin-content">
        <section class="content-header">
            <h1>
                <span translate="">Server settings</span>
            </h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li class="active"><i class="fa fa-cog"></i> <span translate="">Server settings</span></li>
            </ol>
        </section>

        <section class="content">
            <form class="form-horizontal" name="serverForm" ng-submit="mainCtrl.save()">
                <div class="box box-{{serverForm.$valid && 'success' || 'danger'}}">
                    <div class="box-body">
                        <legend>IAM access</legend>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="key"><span translate="">Access key:</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="key" placeholder="Enter Access key" ng-model="settings.config.key" ng-required="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="secret"><span translate="">Secret:</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="secret" placeholder="Enter Secret" ng-model="settings.config.secret" ng-required="true">
                            </div>
                        </div>

                        <legend>Image details</legend>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="image_id"><span translate="">Image id:</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="image_id" placeholder="Enter Image id, ami-xxxx" ng-model="settings.config.image.ImageId" ng-required="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="instance_type"><span translate="">Instance type:</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="instance_type" placeholder="t2.micro" ng-model="settings.config.image.InstanceType" ng-required="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">OS:</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" ng-model="settings.config.image.OS" ng-required="true" ng-options="os as os for os in ['Windows', 'Linux']">
                                    <option value="">Select OS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer with-border">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-flat btn-primary">
                                    <span translate="">Update settings</span>
                                    <i class="fa fa-fw fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
