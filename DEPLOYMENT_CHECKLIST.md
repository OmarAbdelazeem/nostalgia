# Quick Deployment Checklist

## ✅ **Pre-Deployment Checklist**

### **Code Preparation:**
- [ ] ✅ Health check route added (`/api/health`)
- [ ] ✅ Procfile created
- [ ] ✅ railway.json created
- [ ] ✅ render.yaml created
- [ ] ✅ Composer scripts updated
- [ ] ✅ All endpoints tested locally

### **Security:**
- [ ] ✅ `APP_DEBUG=false` in production
- [ ] ✅ New `APP_KEY` generated
- [ ] ✅ Environment variables prepared
- [ ] ✅ HTTPS enabled (automatic)

---

## 🚀 **Deployment Steps**

### **Option 1: Railway (Recommended)**

1. **Sign Up**: [railway.app](https://railway.app)
2. **Connect GitHub**: Link your repository
3. **Create Project**: "Deploy from GitHub repo"
4. **Add Database**: PostgreSQL
5. **Set Variables**:
   ```
   APP_KEY=base64:your-key
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   DB_CONNECTION=postgresql
   ```
6. **Deploy**: Automatic on push
7. **Run Migrations**: `php artisan migrate --force`

### **Option 2: Render**

1. **Sign Up**: [render.com](https://render.com)
2. **Create Web Service**: Connect GitHub repo
3. **Configure**:
   - Environment: PHP
   - Build: `composer install --no-dev --optimize-autoloader`
   - Start: `php artisan serve --host=0.0.0.0 --port=$PORT`
4. **Add Database**: PostgreSQL
5. **Deploy**: Automatic

### **Option 3: Heroku**

1. **Install CLI**: `brew install heroku`
2. **Login**: `heroku login`
3. **Create App**: `heroku create your-app-name`
4. **Add Database**: `heroku addons:create heroku-postgresql:mini`
5. **Set Variables**: `heroku config:set APP_ENV=production`
6. **Deploy**: `git push heroku main`
7. **Migrate**: `heroku run php artisan migrate --force`

---

## 🧪 **Post-Deployment Testing**

### **Health Check:**
```bash
curl https://your-app.railway.app/api/health
```

### **Authentication:**
```bash
curl -X POST https://your-app.railway.app/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### **API Endpoints:**
- [ ] ✅ Health endpoint works
- [ ] ✅ Login works
- [ ] ✅ Categories CRUD works
- [ ] ✅ Products CRUD works
- [ ] ✅ Image upload works
- [ ] ✅ All endpoints return proper responses

---

## 🔧 **Environment Variables**

### **Required Variables:**
```
APP_KEY=base64:your-generated-key
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
DB_CONNECTION=postgresql
```

### **Database Variables (Auto-set):**
```
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

---

## 🚨 **Common Issues**

### **Database Connection:**
- ✅ Use PostgreSQL (not SQLite)
- ✅ Check DB_* variables
- ✅ Run migrations: `php artisan migrate --force`

### **File Uploads:**
- ⚠️ Local storage won't work
- ✅ Use external storage (S3, Cloudinary)
- ✅ Update filesystem config

### **500 Errors:**
- ✅ Check logs: `railway logs` or `heroku logs --tail`
- ✅ Verify APP_KEY is set
- ✅ Check database connection

---

## 📊 **Platform Comparison**

| Platform | Free Tier | Database | Sleep | Ease | Recommendation |
|----------|-----------|----------|-------|------|----------------|
| **Railway** | $5 credit | ✅ PostgreSQL | ❌ | ⭐⭐⭐⭐⭐ | 🥇 **Best** |
| **Render** | 750h/month | ✅ PostgreSQL | ⏰ 15min | ⭐⭐⭐⭐ | 🥈 **Good** |
| **Heroku** | Discontinued | ✅ PostgreSQL | ❌ | ⭐⭐⭐ | 🥉 **OK** |

---

## 🎯 **Quick Start Commands**

### **Generate App Key:**
```bash
php artisan key:generate
```

### **Run Migrations:**
```bash
php artisan migrate --force
```

### **Create Storage Link:**
```bash
php artisan storage:link
```

### **Clear Cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📞 **Support**

### **If Deployment Fails:**
1. Check platform logs
2. Verify environment variables
3. Test locally first
4. Check database connection
5. Review error messages

### **Useful Commands:**
```bash
# Railway
railway logs
railway variables
railway run php artisan migrate --force

# Heroku
heroku logs --tail
heroku config
heroku run php artisan migrate --force

# Render
# Check dashboard for logs and variables
```

---

**Status**: ✅ **READY FOR DEPLOYMENT**  
**Estimated Time**: 30-60 minutes  
**Cost**: $0 (Free) 