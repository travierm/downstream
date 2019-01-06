<div class="container pushFromTop">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label>Display Name</label>
                <input class="form-control" type="text" name="user.display_name" placeholder="Display Name" value="{{ Auth::user()->display_name }}" />
            </div>

            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" name="user.password" placeholder="Password" />
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input class="form-control" type="password" name="user_password_confirm" placeholder="Confirm Password" />
            </div>
        </div>
    </div>
</div>