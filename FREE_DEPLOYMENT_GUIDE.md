# Free Laravel API Deployment Guide

## 🚀 **Free Deployment Options**

**Date**: September 1, 2025  
**Status**: ✅ **Ready for Deployment**  
**Cost**: $0 (Free)  

---

## 📋 **Recommended Free Platforms**

### **🥇 1. Railway (Recommended)**
- **Free Tier**: $5 credit monthly (enough for small projects)
- **Pros**: Easy deployment, automatic HTTPS, custom domains
- **Cons**: Requires credit card for verification

### **🥈 2. Render**
- **Free Tier**: 750 hours/month, 512MB RAM
- **Pros**: Easy setup, automatic deployments, custom domains
- **Cons**: Sleeps after 15 minutes of inactivity

### **🥉 3. Heroku**
- **Free Tier**: Discontinued, but still good for small projects
- **Pros**: Excellent documentation, easy scaling
- **Cons**: No longer truly free

### **🏅 4. Vercel**
- **Free Tier**: Generous limits, great performance
- **Pros**: Fast, automatic deployments, edge functions
- **Cons**: Better for frontend, but works for APIs

---

## 🚀 **Option 1: Railway Deployment (Recommended)**

### **Step 1: Prepare Your Project**

#### **1.1 Create Procfile**
Create a file named `Procfile` (no extension) in your project root:
```
web: vendor/bin/heroku-php-apache2 public/
```

#### **1.2 Update .env.example**
```bash
# Copy .env to .env.example and update for production
cp .env .env.example
```

Edit `.env.example`:
```env
APP_NAME="Laravel Nostalgia API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=postgresql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

#### **1.3 Update composer.json**
Add this to your `composer.json`:
```json
{
    "scripts": {
        "post-install-cmd": [
            "php artisan key:generate",
            "php artisan migrate --force",
            "php artisan storage:link"
        ]
    }
}
```

#### **1.4 Create railway.json**
Create `railway.json` in your project root:
```json
{
    "$schema": "https://railway.app/railway.schema.json",
    "build": {
        "builder": "NIXPACKS"
    },
    "deploy": {
        "startCommand": "php artisan serve --host=0.0.0.0 --port=$PORT",
        "healthcheckPath": "/api/health",
        "healthcheckTimeout": 100,
        "restartPolicyType": "ON_FAILURE",
        "restartPolicyMaxRetries": 10
    }
}
```

#### **1.5 Add Health Check Route**
Add this to your `routes/api.php`:
```php
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});
```

### **Step 2: Deploy to Railway**

#### **2.1 Sign Up for Railway**
1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub
3. Add credit card (required for verification)

#### **2.2 Create New Project**
1. Click "New Project"
2. Select "Deploy from GitHub repo"
3. Choose your repository
4. Railway will automatically detect Laravel

#### **2.3 Configure Environment Variables**
In Railway dashboard, add these variables:
```
APP_KEY=base64:your-generated-key
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
DB_CONNECTION=postgresql
```

#### **2.4 Add PostgreSQL Database**
1. Click "New" → "Database" → "PostgreSQL"
2. Railway will automatically set DB_* variables
3. Your app will connect automatically

#### **2.5 Deploy**
1. Railway will automatically deploy when you push to GitHub
2. Or click "Deploy" manually
3. Wait for deployment to complete

#### **2.6 Run Migrations**
In Railway dashboard:
1. Go to your app
2. Click "Variables" tab
3. Add custom command: `php artisan migrate --force`

---

## 🌐 **Option 2: Render Deployment**

### **Step 1: Prepare Your Project**

#### **1.1 Create render.yaml**
Create `render.yaml` in your project root:
```yaml
services:
  - type: web
    name: laravel-nostalgia-api
    env: php
    plan: free
    buildCommand: composer install --no-dev --optimize-autoloader
    startCommand: php artisan serve --host=0.0.0.0 --port=$PORT
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: false
      - key: LOG_CHANNEL
        value: stack
      - key: CACHE_DRIVER
        value: file
      - key: SESSION_DRIVER
        value: file
      - key: QUEUE_CONNECTION
        value: sync
    healthCheckPath: /api/health

databases:
  - name: laravel-nostalgia-db
    databaseName: laravel_nostalgia
    user: laravel_user
    plan: free
```

### **Step 2: Deploy to Render**

#### **2.1 Sign Up for Render**
1. Go to [render.com](https://render.com)
2. Sign up with GitHub
3. No credit card required

#### **2.2 Create New Web Service**
1. Click "New" → "Web Service"
2. Connect your GitHub repository
3. Render will auto-detect Laravel
4. Configure:
   - **Name**: `laravel-nostalgia-api`
   - **Environment**: `PHP`
   - **Build Command**: `composer install --no-dev --optimize-autoloader`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

#### **2.3 Add Environment Variables**
```
APP_KEY=base64:your-generated-key
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
DB_CONNECTION=postgresql
```

#### **2.4 Create Database**
1. Click "New" → "PostgreSQL"
2. Connect it to your web service
3. Render will auto-set DB variables

#### **2.5 Deploy**
1. Click "Create Web Service"
2. Render will build and deploy automatically
3. Wait for deployment to complete

---

## 🐘 **Option 3: Heroku Deployment**

### **Step 1: Install Heroku CLI**
```bash
# macOS
brew tap heroku/brew && brew install heroku

# Windows
# Download from https://devcenter.heroku.com/articles/heroku-cli
```

### **Step 2: Prepare Your Project**

#### **2.1 Create Procfile**
```
web: vendor/bin/heroku-php-apache2 public/
```

#### **2.2 Update composer.json**
```json
{
    "scripts": {
        "post-install-cmd": [
            "php artisan key:generate",
            "php artisan migrate --force",
            "php artisan storage:link"
        ]
    }
}
```

### **Step 3: Deploy to Heroku**

#### **3.1 Login to Heroku**
```bash
heroku login
```

#### **3.2 Create Heroku App**
```bash
heroku create your-app-name
```

#### **3.3 Add PostgreSQL**
```bash
heroku addons:create heroku-postgresql:mini
```

#### **3.4 Set Environment Variables**
```bash
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_URL=https://your-app-name.herokuapp.com
```

#### **3.5 Deploy**
```bash
git add .
git commit -m "Deploy to Heroku"
git push heroku main
```

#### **3.6 Run Migrations**
```bash
heroku run php artisan migrate --force
```

---

## 🔧 **Pre-Deployment Checklist**

### **✅ Code Preparation:**
- [ ] Remove debug code
- [ ] Update environment variables
- [ ] Test all endpoints locally
- [ ] Ensure database migrations work
- [ ] Check file upload functionality

### **✅ Security:**
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Use strong database passwords
- [ ] Enable HTTPS (automatic on most platforms)

### **✅ Performance:**
- [ ] Optimize composer autoloader
- [ ] Enable caching
- [ ] Optimize images
- [ ] Use CDN for static files

---

## 📊 **Platform Comparison**

| Platform | Free Tier | Database | Custom Domain | SSL | Sleep | Ease |
|----------|-----------|----------|---------------|-----|-------|------|
| **Railway** | $5 credit | ✅ PostgreSQL | ✅ | ✅ | ❌ | ⭐⭐⭐⭐⭐ |
| **Render** | 750h/month | ✅ PostgreSQL | ✅ | ✅ | ⏰ 15min | ⭐⭐⭐⭐ |
| **Heroku** | Discontinued | ✅ PostgreSQL | ✅ | ✅ | ❌ | ⭐⭐⭐ |
| **Vercel** | Generous | ❌ (external) | ✅ | ✅ | ❌ | ⭐⭐⭐ |

---

## 🎯 **Recommended Deployment Strategy**

### **For Development/Testing:**
- **Platform**: Render
- **Reason**: No credit card required, easy setup

### **For Production:**
- **Platform**: Railway
- **Reason**: Better performance, no sleep, reliable

### **For Learning:**
- **Platform**: Heroku
- **Reason**: Excellent documentation, good learning experience

---

## 🚨 **Important Notes**

### **File Storage:**
- **Local storage** won't work on free platforms
- **Use external storage** like AWS S3 or Cloudinary
- **Update filesystem config** for production

### **Database:**
- **SQLite** won't work on most platforms
- **Use PostgreSQL** (provided by platforms)
- **Backup your data** regularly

### **Environment Variables:**
- **Never commit** `.env` files
- **Use platform** environment variables
- **Keep secrets** secure

### **Performance:**
- **Free tiers** have limitations
- **Monitor** resource usage
- **Optimize** for production

---

## 🔄 **Post-Deployment Steps**

### **1. Test Your API**
```bash
# Test health endpoint
curl https://your-app-name.railway.app/api/health

# Test authentication
curl -X POST https://your-app-name.railway.app/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### **2. Update Frontend**
- Update API base URL in frontend
- Test all endpoints
- Verify file uploads work

### **3. Monitor Performance**
- Check response times
- Monitor error logs
- Set up alerts

### **4. Set Up Custom Domain (Optional)**
- Add custom domain in platform dashboard
- Update DNS records
- Enable SSL certificate

---

## 📞 **Troubleshooting**

### **Common Issues:**

#### **1. Database Connection Errors**
```bash
# Check database variables
heroku config | grep DB
# or
railway variables
```

#### **2. 500 Internal Server Error**
```bash
# Check logs
heroku logs --tail
# or
railway logs
```

#### **3. File Upload Issues**
- Use external storage (S3, Cloudinary)
- Update filesystem configuration
- Check file permissions

#### **4. Migration Errors**
```bash
# Run migrations manually
heroku run php artisan migrate --force
# or
railway run php artisan migrate --force
```

---

## 🎉 **Success Checklist**

### **✅ Deployment Complete When:**
- [ ] API is accessible via HTTPS
- [ ] Health endpoint returns 200
- [ ] Authentication works
- [ ] Database migrations completed
- [ ] File uploads work (if using external storage)
- [ ] All CRUD operations work
- [ ] Frontend can connect to API

---

**Status**: ✅ **READY FOR DEPLOYMENT**  
**Recommended Platform**: Railway  
**Estimated Time**: 30-60 minutes  
**Cost**: $0 (Free) 