<div class="card" style="max-width: 480px; margin: 40px auto;">
    <div class="card-header">
        <h3>Register</h3>
    </div>
    <form data-api="auth/register" method="POST" data-clear-on-success="false">
        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
        </div>
        <button type="submit" class="btn">Create Account</button>
    </form>
    <p class="mt-10">Already have an account? <a href="#" data-link="login">Login</a></p>
</div>
