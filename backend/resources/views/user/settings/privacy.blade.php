<div class="container pushFromTop">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Should your account be public?</label>
                <br />
                <select class="col-lg-3 form-control form-control-lg" name="privacy.private_account">
                    <option @if($account_open == 'yes') selected @endif value="yes">Yes</option>
                    <option @if($account_open == 'no') selected @endif value="no">No</option>
                </select>
                <small id="passwordHelpInline" class="text-muted">
                    Yes to let other users follow and see your collection, No and your account will not show anywhere.
                    <br /><br />
                    Your data such as email and session history is not shared publicly or with 3rd parties.
                </small>
            </div>
        </div>
    </div>
</div>