# Laravel Blog — Interview Assignment

A production-ready Laravel 11 blog with authentication (email + Google/Facebook), roles & permissions, posts and comments (soft deletes), admin panel, caching/eager loading, custom middleware and service provider, and tests with Pest. This README is **copy‑paste friendly** so you can set up and run quickly.

---

## 🧰 Tech Stack

- **PHP** 8.2+
- **Laravel** 11
- **MySQL** 8+ (MariaDB OK)
- **Node** 20+ / **npm**
- **Frontend**: Blade + Tailwind (via Breeze)
- **Packages**: `laravel/breeze`, `laravel/socialite`, `spatie/laravel-permission`, `pestphp/pest`

---

## 🚀 Quick Start

```bash
# 1) Create project
composer create-project laravel/laravel blogx
cd blogx

# 2) Env + key
cp .env.example .env
php artisan key:generate

# 3) Configure DB in .env
# DB_DATABASE=blogx
# DB_USERNAME=root
# DB_PASSWORD=

# 4) Auth scaffolding
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run build   # or: npm run dev

# 5) Core migrations
php artisan migrate

# 6) Packages
composer require laravel/socialite spatie/laravel-permission

# 7) Publish + migrate permissions tables
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# 8) Seed roles
php artisan make:seeder RoleSeeder
# (Ensure seeder code below is added, then:)
php artisan db:seed --class=RoleSeeder

# 9) Serve app
php artisan serve
```

---

## 🔐 Social Login (Google & Facebook)

Add these to `config/services.php`:

```php
'google' => [
  'client_id' => env('GOOGLE_CLIENT_ID'),
  'client_secret' => env('GOOGLE_CLIENT_SECRET'),
  'redirect' => env('GOOGLE_REDIRECT_URI'),
],
'facebook' => [
  'client_id' => env('FACEBOOK_CLIENT_ID'),
  'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
  'redirect' => env('FACEBOOK_REDIRECT_URI'),
],
```

Add to `.env` (replace with your console credentials):

```dotenv
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

Routes:

```php
Route::get('/auth/{provider}', [SocialAuthController::class,'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class,'callback'])->name('social.callback');
```

---

## 👥 Roles & Permissions

**Seeder (`database/seeders/RoleSeeder.php`)**

```php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'User']);
    }
}
```

Run:

```bash
php artisan db:seed --class=RoleSeeder
```

**Promote yourself to Admin**

```bash
php artisan tinker
>>> $u = \App\Models\User::first();
>>> $u->assignRole('Admin');
```

Use middleware in admin routes:

```php
Route::prefix('admin')->name('admin.')->middleware(['auth','role:Admin'])->group(function () {
    Route::get('/', [DashboardController::class,'__invoke'])->name('dashboard');
    Route::resource('users', UserController::class)->only(['index','edit','update','destroy']);
    Route::resource('posts', AdminPostController::class)->only(['index','destroy']);
});
```

---

## 🗄️ Database & Models

Create models + migrations:

```bash
php artisan make:model Post -m
php artisan make:model Comment -m
```

**Posts migration**

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('content');
    $table->softDeletes();
    $table->timestamps();
});
```

**Comments migration**

```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->text('body');
    $table->timestamps();
});
```

**Models**

```php
// app/Models/Post.php
use Illuminate\Database\Eloquent\SoftDeletes;
class Post extends Model {
  use HasFactory, SoftDeletes;
  protected $fillable = ['title','content','user_id'];
  public function author(){ return $this->belongsTo(User::class,'user_id'); }
  public function comments(){ return $this->hasMany(Comment::class); }
}

// app/Models/Comment.php
class Comment extends Model {
  use HasFactory;
  protected $fillable = ['post_id','user_id','body'];
  public function post(){ return $this->belongsTo(Post::class); }
  public function author(){ return $this->belongsTo(User::class,'user_id'); }
}
```

Add roles trait to user:

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable {
  use HasApiTokens, HasFactory, Notifiable, HasRoles;
}
```

Run:

```bash
php artisan migrate
```

---

## 🧭 Controllers, Requests, Routes

```bash
php artisan make:request StorePostRequest
php artisan make:request UpdatePostRequest
php artisan make:controller PostController --resource --model=Post
php artisan make:controller CommentController --model=Comment
php artisan make:controller SocialAuthController
php artisan make:policy PostPolicy --model=Post
```

**Validation (examples)**

```php
// StorePostRequest
public function rules(){ return ['title'=>'required|max:255','content'=>'required']; }

// UpdatePostRequest (owner or admin)
public function authorize(){
  $post = $this->route('post');
  return auth()->check() && (auth()->user()->hasRole('Admin') || $post->user_id === auth()->id());
}
```

**Routes (`routes/web.php`)**

```php
Route::view('/', 'welcome');
require __DIR__.'/auth.php'; // Breeze

// Public
Route::resource('posts', PostController::class)->only(['index','show']);

// Authenticated
Route::middleware('auth')->group(function() {
  Route::resource('posts', PostController::class)->except(['index','show']);
  Route::post('/posts/{post}/comments', [CommentController::class,'store'])->name('comments.store');
  Route::delete('/posts/{post}/comments/{comment}', [CommentController::class,'destroy'])->name('comments.destroy');
});
```

---

## 🧱 Middleware & Provider

```bash
php artisan make:middleware ActivityLogger
php artisan make:provider BusinessLogicServiceProvider
```

**ActivityLogger** logs user, IP, path and status for each web request.  
**BusinessLogicServiceProvider** registers a simple stats service (`users`, `posts`, `comments`) for the admin dashboard.

Register middleware in `app/Http/Kernel.php` (web group).  
Register provider in `config/app.php` providers array.

---

## 🖥️ Admin Panel

- Dashboard with counts from the `stats.service`
- Manage users (index/edit/update/destroy)
- Moderate posts (index/destroy)
- Route group is protected by `auth` + `role:Admin`

---

## 📦 Caching & Eager Loading

- Posts index uses `cache()->remember()` with pagination key and optional search query.
- Eager load `author` and `comments.author` in controllers to avoid N+1.
- Clear/flush cache after create/update/delete as needed.

---

## 🧪 Testing (Pest)

```bash
composer require pestphp/pest --dev
php artisan pest:install
php artisan test
```

Example test ideas:
- Post CRUD happy path
- Comment create/delete permissions
- Middleware basic assertion (spy Log or assert OK)

---

## 📁 Folder Structure (excerpt)

```
app/
  Http/
    Controllers/
      Admin/
        AdminPostController.php
        DashboardController.php
        UserController.php
      CommentController.php
      PostController.php
      SocialAuthController.php
    Middleware/ActivityLogger.php
    Requests/StorePostRequest.php
    Requests/UpdatePostRequest.php
  Models/{Post.php, Comment.php, User.php}
  Policies/PostPolicy.php
  Providers/BusinessLogicServiceProvider.php
resources/
  views/
    admin/{dashboard.blade.php, users/*, posts/*}
    posts/{index.blade.php, show.blade.php}
routes/{web.php, api.php}
```

---

## 🧩 NPM Scripts

```bash
npm run dev    # Vite dev server (HMR)
npm run build  # Production build
```

---

## 🐛 Troubleshooting

- **SQLSTATE[HY000] [1049] Unknown database** → Create DB and update `.env`.
- **npm not found / build fails** → Install Node 20+, delete `node_modules`, re‑run `npm install`.
- **Social login 403/redirect mismatch** → Ensure OAuth redirect URLs match exactly in provider console and `.env`.
- **403 on admin** → Ensure your user has `Admin` role (see tinker snippet above).

---

## ✅ What to Highlight in the Interview

- Clean, layered structure with policies/requests/middleware.
- Proper route grouping, model binding, and authorization.
- Caching + eager loading for performance.
- Extensibility: provider pattern, admin area, Socialite integration.
- Green test suite with Pest.

---

## 📄 License

MIT (or your preferred license).
