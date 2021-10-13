<a class="btn btn-primary" href="/avatar"><span class="material-icons">account_box</span> Change avatar</a>
<a class="btn btn-primary" href="/delete-account"><span class="material-icons">delete_forever</span> Delete account</a>

<div class="divider"></div>

<form onsubmit="return confirm('Do you really want to do this?');" action="/settings" method="post">
    <label class="mb-3">Change password</label>
    <div class="form-floating mb-3">
        <input placeholder="old password" type="password" class="form-control" name="old-password" required>
        <label for="old-password">Old password</label>
    </div>

    <div class="form-floating mb-3">
        <input placeholder="new password" type="password" id="new-password" class="form-control" aria-describedby="passwordHelpBlock" name="new-password" required>
        <label for="new-password">New password</label>
        <div id="passwordHelpBlock" class="form-text">
            Your password must be at least 3 characters long.
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><span class="material-icons">check</span> Submit</button>
</form>