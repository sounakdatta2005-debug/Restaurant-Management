<div class="card" style="max-width: 480px; margin: 40px auto;">
    <div class="card-header">
        <h3>Login</h3>
    </div>
    <form data-api="auth/login" method="POST" data-clear-on-success="false">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn">Sign In</button>
    </form>
    <p class="mt-10">No account yet? <a href="#" data-link="register">Register</a></p>
</div>
