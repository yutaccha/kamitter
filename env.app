APP_NAME=Kam1tter
APP_ENV=local
APP_KEY=base64:hRYW1m7clnHwWoDikVt+V8KuIkX6u+JnSz+j8pz62Jw=
APP_DEBUG=true
APP_URL=http://kamitter.test

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=kamitter
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=4a9f563b5d85a5
MAIL_PASSWORD=d81c8ab6a2db83
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME=kam1tter
MAIL_ENCRYPTION=null

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

TWITTER_CLIENT_ID=NhRAfKTErdaYet8z2JEfaYxbc
TWITTER_CLIENT_SECRET=IUaYk2YSsFnSQR9WuDYbbi5pTdtzDF3J3pi8240uk3mGxmyKNI
CALLBACK_URL=http://kamitter.test/auth/twitter/callback