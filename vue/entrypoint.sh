cd /var/www
cp .env.example .env.production
npm i
npm run build
cp .htaccess.example ./dist/.htaccess
ln -s dist html
apache2-foreground
