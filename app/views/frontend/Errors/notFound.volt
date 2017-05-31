<div ng-include="'home/welcome.tpl.html'" class="ng-scope">
    <div class="row welcome ng-scope">
        <div class="col-md-8">
            <h1 ng-if="!isLoggedIn" class="text-left ng-binding ng-scope">Page 404</h1>
            <div>
                <p ng-if="!isLoggedIn" class="text-left ng-binding ng-scope">
                  We can not find the page you're looking for.
                </p>
            </div>
        </div>
    </div>
</div>