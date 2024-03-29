#
##コマンド
docker-compose up -d nginx postgres nuxt

cd ../laradock/
docker-compose up nginx postgres
docker-compose exec workspace bash


cd ../laradock/
docker-compose up -d nuxt
docker-compose exec nuxt sh

php artisan migrate:reset
php artisan migrate:refresh --seed
php artisan passport:install


## heroku コマンド

heroku run "php artisan migrate:reset"
heroku run "php artisan migrate"
heroku run "php artisan migrate:refresh --seed"
heroku run "php artisan passport:install"
git push heroku master

再起動
 heroku restart

 heroku内に入る(作ったファイル出たら消えるパターン)
 heroku run bash
 
## \<laravel>


# 最終的な目標
・docker で サービスを稼働する
・複数サービスを稼働するか否か　
　 → 複数サービスはまだ先　
　 → 分けたほうが管理しやすい
　 → MVPで行こう！　まずは勉強
・laradock使うか
→ 




# Laravel

▼公式
http://laradock.io/


    1 - Clone Laradock inside your PHP project:

    git clone https://github.com/Laradock/laradock.git

    2 - Enter the laradock folder and rename env-example to .env.
    (cd laradock)
    cp env-example .env

    3 - Run your containers:

    docker-compose up -d nginx mysql phpmyadmin redis workspace 

    4 - Open your project’s .env file and set the following:

        DB_HOST=mysql
        REDIS_HOST=redis
        QUEUE_HOST=beanstalkd

    4-2 - change your DB info:

        POSTGRES_DB=twimon
        POSTGRES_USER=postgres
        POSTGRES_PASSWORD=postgres

    5 - Open your browser and visit localhost: http://localhost.

    That's it! enjoy :)

ここら辺でこけたりすることもあるのでその場合は一度docker内をクリーンな状態にして再度試す。
    - イメージを全削除する
    docker images -aq | xargs docker rmi

    - 未使用ボリューム削除
    docker volume prune

    - 未使用ネットワーク削除
    docker network prune

ちなみに2019/01/09時点でこの手順を試すとエラーになった。
https://github.com/laradock/laradock/issues/1940

原因はGithubに上がっているソース自体に問題があったため。
・Github上のissuesで同じ問題が起きている人がいないか
・Pull request　で問題を解消するためのコードを提案している人がいないか
を確認し、いる場合は大人しく待つ

※2019/01/10時点でpull requestがマージされて解消されています。

このように元からエラーになる場合はいくらdockerをクリーンな状態にしてやり直しても同じ結果になるので原因をしっかり追求する(githubを読む)ことをオススメします。

## (1)コンテナ起動
    docker-compose up -d nginx mysql phpmyadmin redis workspace 

## (2)コンテナ内に入る
    docker exec -it laradock_workspace_1 /bin/bash
 
 or 

    docker-compose exec workspace bash

## (3)プロジェクト作成

/var/www# composer create-project laravel/laravel Lapp

composer create-project laravel/laravel Lapp

すると初めに
Do not run Composer as root/super user! See https://getcomposer.org/root for details
と黄色文字で出るが問題ないですしばし待ちましょう

## (4)NGINXのportを変える

    - NGINX_HOST_HTTP_PORT=80
    + NGINX_HOST_HTTP_PORT=8001

## ・ブラウザで動作確認
http://localhost:8001

## ・.envのローカルファイルとdockerのコンテナのディレクトリが紐づいてるとこの設定

        - APP_CODE_PATH_HOST=../
        + APP_CODE_PATH_HOST=../lapp/

        - DATA_PATH_HOST=~/.laradock/data
        + DATA_PATH_HOST=.laradock/data

▼Laradockが上手く動かなくて困った話

https://qiita.com/skmt719/items/a296d81150fd7319e71a

## ・コンテナから脱出 停止＆再起動
    exit
    docker-compose stop
    docker-compose up -d nginx mysql phpmyadmin redis workspace

    今回はビルドしないのでめっちゃ早いです。



# <nuxt.js>


## ・nuxt はポート3000 を使うので、あらかじめ開けておく
docker-compose.yml

      ports:
        - "${WORKSPACE_SSH_PORT}:22"
        - 3000:3000 ← これ

## ・コンテナ再起動
`docker-compose up -d nginx mysql workspace `

## ・workspace コンテナへ再アクセス。

`docker-compose exec workspace bash`

## ・テンプレートを使ってnuxtを導入
`vue init nuxt-community/starter-template nuxt`

    /var/www# vue init nuxt-community/starter-template nuxt

        Command vue init requires a global addon to be installed.
        Please run yarn global add @vue/cli-init and try again.

## ・global に追加
`yarn global add @vue/cli-init`
した後も同じエラーがでるので
`npm install -g @vue/cli-init`


## ・[再]テンプレートを使ってnuxtを導入
vue init nuxt-community/starter-template nuxt

? Project name nuxt
? Project description Nuxt.js project
? Author 

とそれぞれ聞かれるのでエンターでやり過ごす

## ・削除＆ファイル移動
    /var/www# rm package.json # laravelインストール時に生成されるやつは不要。
    /var/www# mv nuxt/package.json package.json #代わりにこっち使う。
    /var/www# mv nuxt/nuxt.config.js nuxt.config.js

## ・npm intall
    /var/www# npm install

## ・yarn intall
    /var/www# yarn install

## ・package.json 編集

    {
        "name": "nuxt",
        ~ 省略 ~
        "scripts": {
            "dev": "nuxt",                                                  <- before
            "dev": "HOST=0.0.0.0 PORT=3000 node_modules/nuxt/bin/nuxt",     <- after
            "dev": "HOST=0.0.0.0 PORT=3000 nuxt",                           <- after2
            "build": "nuxt build",
            "start": "nuxt start",
            "generate": "nuxt generate",
            "lint": "eslint --ext .js,.vue --ignore-path .gitignore .",
            "precommit": "npm run lint"
        },
        ~ 省略 ~
    }

## ・nuxt起動

    # yarn dev

## ・Nuxtへアクセス
http://localhost:3000/



▼ローカルにある Laravel を公開サーバーにデプロイ
https://laraweb.net/environment/3192/



▼ConoHaを使ってLaravelを超絶簡単にインストールしてみた

https://www.youtube.com/watch?v=oBJFCTu7H1s


ssh root@118.27.4.161

cd /var/www/html/laravel

[root@118-27-4-161 laravel]# ls
app        composer.json  database      public     routes      tests
artisan    composer.lock  package.json  readme.md  server.php  vendor
bootstrap  config         phpunit.xml   resources  storage     webpack.mix.js
[root@118-27-4-161 laravel]# 



-----------------------------------------------------

# mysql

▼Laradockで簡単アプリ作成しようとしたら、速攻でDB周り／日本語問題で躓いたけど、チャチャッと解決
https://qiita.com/yamazaki/items/d9d3c56f8058ec1e5f65

▼よく使うMySQLコマンド集
https://qiita.com/CyberMergina/items/f889519e6be19c46f5f4



(1)laradock/.envを修正

    MYSQL_VERSION=latest
    MYSQL_DATABASE=default
    MYSQL_USER=default
    MYSQL_PASSWORD=secret
    MYSQL_PORT=3306
    MYSQL_ROOT_PASSWORD=root

    ↓↓↓↓↓↓↓↓↓↓


    MYSQL_VERSION=5.7
    MYSQL_DATABASE=todo
    MYSQL_USER=mysql
    MYSQL_PASSWORD=mysql
    MYSQL_PORT=3306
    MYSQL_ROOT_PASSWORD=root


▼LaradockのMySQLに接続できなくてはまった話
https://qiita.com/dnrsm/items/4bd078c17bb0d6888647

(2) .envを反映させる

## volumeなど　削除 
docker rmi laradock_mysql
docker volume rm laradock_mysql
rm -rf ~/.lardock/data/mysql ※消えなかったので手動削除で対応

## 再ビルド
docker-compose build --no-cache mysql

## docker-compose upして、mysqlのインスタンスに入る
docker-compose exec mysql bash

## mysqlの対象のDBに入れれば想定通り
mysql -u default -p 
secret


(3) Lapp/.envを更新

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret

    ↓↓↓↓↓↓↓↓↓↓

    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=todo
    DB_USERNAME=mysql
    DB_PASSWORD=mysql


▼Laradock+MySQL マイグレーションするまで
https://qiita.com/kengo_9990/items/4fdbfbd47b2ba32f9f35

▼Laravelの開発環境をLaradockを使って構築する
https://qiita.com/ucan-lab/items/90f74ce801618830e4fc#laradock-mysql%E3%82%B3%E3%83%B3%E3%83%86%E3%83%8A

(4)migration いっちゃう
    root@13f4f487de87:/var/www# php artisan migrate
    Migration table created successfully.
    Migrating: 2014_10_12_000000_create_users_table
    Migrated:  2014_10_12_000000_create_users_table
    Migrating: 2014_10_12_100000_create_password_resets_table
    Migrated:  2014_10_12_100000_create_password_resets_table
    Migrating: 2019_05_13_001641_create_folders_table
    Migrated:  2019_05_13_001641_create_folders_table



--------------------------------------------------------------------

# 最初の操作 laradock配下
    docker-compose up -d nginx postgres
    docker-compose exec workspace bash



# おどれーたこと
・value 属性の値には old('title') の実行結果を展開しています。入力エラーがあったとき、入力値はセッションに一時的に保存されます。Laravel が提供する old 関数はそのセッション値を取得します。引数は取得したい入力欄の name 属性です。

# 気になったこと
入門Laravelチュートリアル
（7）
TaskController.php
public function edit(int $id, int $task_id, EditTask $request)
{
    ~~~
}
のところ。EditTaskクラスが定義されていないので怒られた。

use App\Http\Requests\CreateTask;
と同様に
use App\Http\Requests\EditTask;
を記述するとOK。







# laradock DB変更 mysql → PostgreSql

(1)laradock .env 

    # POSTGRES_DB=default
    POSTGRES_DB=sample_db
    POSTGRES_USER=default
    POSTGRES_PASSWORD=secret
    # POSTGRES_PORT=5432
    POSTGRES_PORT=54320
    POSTGRES_ENTRYPOINT_INITDB=./postgres/docker-entrypoint-initdb.d


#heroku
(1)herokuのcliを使えるようにする(ローカルにて)
    
    $ brew tap heroku/brew && brew install heroku

    ▼heroku 公式サイト
    https://devcenter.heroku.com/articles/heroku-cli

(の前にbrew install)
https://brew.sh/index_ja


(2)ログイン
   $ heroku login

(3)create
    Lapp $ heroku create
    Creating app... done, ⬢ stark-ravine-37952
    https://stark-ravine-37952.herokuapp.com/ | https://git.heroku.com/stark-ravine-37952.git

(4)postgres追加 (heroku作業)

(5)コマンドラインで接続情報を確認

    $ heroku config:get DATABASE_URL

(6)設定
postgres://DB_USERNAME:DB_PASSWORD@DB_HOST:5432/DB_DATABASE
を参考に。
postgres://xxxxx:yyyyy@ec2-zz-zz-zzz-zz.compute-1.amazonaws.com:5432/abcd1234

$ heroku config:set DB_CONNECTION=pgsql
$ heroku config:set DB_HOST=ec2-zz-zz-zzz-zz.compute-1.amazonaws.com
$ heroku config:set DB_DATABASE=abcd1234
$ heroku config:set DB_USERNAME=xxxxx
$ heroku config:set DB_PASSWORD=yyyyy


-- backend
postgres://ukcvpsaayhazpj:4d28e25d3c8740dd7577048afc3a0d89a981f63cfd4db91f3a25c59224da2527@ec2-174-129-41-127.compute-1.amazonaws.com:5432/deec28gf8v85hb

heroku config:set APP_DEBUG=true

$ heroku config:set DB_CONNECTION=pgsql
$ heroku config:set DB_HOST=ec2-174-129-41-127.compute-1.amazonaws.com
$ heroku config:set DB_DATABASE=deec28gf8v85hb
$ heroku config:set DB_USERNAME=ukcvpsaayhazpj
$ heroku config:set DB_PASSWORD=4d28e25d3c8740dd7577048afc3a0d89a981f63cfd4db91f3a25c59224da2527

(7)SendGrid アドオン登録

・クレカ登録
・verification 用でsms認証

(8)APP KEY
heroku config:set APP_KEY=$(php artisan --no-ansi key:generate --show)


(9)migration 実行
heroku run "php artisan migrate"


# heroku 運営編

 heroku内に入る(作ったファイル出たら消えるパターン)
 heroku run bash

 heroku 更新
 git push heroku master


## Internal server エラー

 heroku内に入る
 heroku ps:exec -a twimon-backend

▼[PHP] heroku でプライベートアクセストークンが生成できないエラーがおきたときの解消法
https://qiita.com/penton310/items/6d4d3d735801443ebd53

    ls -la storage/
    chmod -R 777 storage
    php artisan passport:keys


##複数環境でのheroku deploy
git remote set-url heroku git@heroku.com:twimon-backend.git

※gitのremote先がデフォルトのheroku app指定になる
※直接指定する場合は--app　オプションを付ける　
    ex) heroku config:get DATABASE_URL --app twimon-backend

## heroku push
git push heroku master

## 初期設定
heroku run "php artisan migrate:reset"
heroku run "php artisan migrate:refresh --seed"
heroku run "php artisan passport:install"

# lighthouse

https://github.com/nuwave/lighthouse

$ composer require nuwave/lighthouse
$ php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=schema
$ composer require mll-lab/laravel-graphql-playground

# cors
https://github.com/barryvdh/laravel-cors

#seeds 
・DB初期化
php artisan migrate:reset
・DB作成
php artisan migrate

php artisan migrate:refresh --seed

php artisan make:seeder UsersTableSeeder
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=CommentsTableSeeder
php artisan db:seed --class=LikesTableSeeder
php artisan db:seed --class=TalksTableSeeder

・autoload
composer dump-autoload

# twitter login
▼ Nuxt.jsとLaravelを使ってTwitterログイン機能を実装する
https://qiita.com/hareku/items/ea09602bf40bf0a42040

▼Laravel 5.5 Laravel Socialite
https://readouble.com/laravel/5.5/ja/socialite.html


## socialite
(1) composer require laravel/socialite

(2) .env

(3) services.php

'twitter' => [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'redirect' => env('TWITTER_URL'),
],

(4)config/cors.php


    'supportsCredentials' => true,
    'allowedOrigins' => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,




(5)config/lighthouse.php つくってなかったので足す
php artisan vendor:publish --provider="Nuwave\Lighthouse\LighthouseServiceProvider" --tag=config

(6)これ足す
 'route' => [
        'prefix' => '',
        'middleware' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Nuwave\Lighthouse\Support\Http\Middleware\AcceptJson::class,
        ],
    ],


## コールバック

▼Laravel + Socialite + Vue(Nuxt)でtwitter認証をしようとするとsession周りで躓きます。
https://teratail.com/questions/124977

これ足す
    TWITTER_ACCESS_TOKEN=2578449780-tpnLnmqJ6wrusZqhSSDJYrbPrgc21R08Z6Qwra9
    TWITTER_ACCESS_TOKEN_SECRET=8zQG2b1tu3SzoV04BiluEr0kZY9r5zNB48xxaNnuLdHpo


$user = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));



コールバックURL例
https://choicemaker-e052f.firebaseapp.com/__/auth/handler?oauth_token=iavRawAAAAAA9tkiAAABa4w_oks&oauth_verifier=RTNuPHYvGB3jsySLmN53vfUXVNUB48Ow


▼旧CallbackURL
https://choicemaker-e052f.firebaseapp.com/__/auth/handler

http://localhost:8001/og/1
http://localhost:8001/og/twitter/callback




## 







# Laravelで画像編集
▼完全網羅！Intervention Image（PHP）で画像を編集する全実例
 https://blog.capilano-fw.com/?p=1574

 (1)Intervention Imageをインストールする
    composer require intervention/image

 (2)storage配下に必要なファイルを用意する
 storage/app/fonts/◯◯◯.ttf
 storage/app/images/◯◯◯.png
 
 ▼Laravel5.6 + Intervention Image でサーバサイドで画像加工を行う
 https://qiita.com/Yuuki_Takahasi/items/3ecd6a4d9768b4efb826



 # Passport
▼Laravel 5.6 API認証(Passport)
https://readouble.com/laravel/5.6/ja/passport.html

▼Laravel Passport
https://laravel.com/docs/5.8/passport#creating-a-personal-access-client

▼Laravel Passportの使い方まとめ
https://qiita.com/zaburo/items/65de44194a2e67b59061

▼GraphQL Auth with Passport and Lighthouse PHP
https://ditecnologia.com/2019/06/24/graphql-auth-with-passport-and-lighthouse-php/

composer require laravel/passport
php artisan passport:install
php artisan migrate
php artisan passport:client --personal

 

 curl -H 'Accept: application/json' -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdjMTQyMGU0ZTNhYjJmYmY4ZDFkZTVlNTBiNDFiMThiZTMwODBjZjdmNTJhYmRiZWNlOTQ4NmIxNTk1OWI5YzE2OTBkNWEzNWQzZDNiMWYzIn0.eyJhdWQiOiIxIiwianRpIjoiN2MxNDIwZTRlM2FiMmZiZjhkMWRlNWU1MGI0MWIxOGJlMzA4MGNmN2Y1MmFiZGJlY2U5NDg2YjE1OTU5YjljMTY5MGQ1YTM1ZDNkM2IxZjMiLCJpYXQiOjE1NjIzMDE3NjAsIm5iZiI6MTU2MjMwMTc2MCwiZXhwIjoxNTkzOTI0MTYwLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.vP9wYfADeTuoVAkVS8wtWougIBNL0Rk2x6yo1j9xqQ_-AvfxEEuww2tvPsZgDblGrddv51jcN98LR954akkukyWv2wcSCE2JAlOZQL7Z4kYFPuGVQ7kupPaugOxMk_NsYd_BaoRoi4B8f80jZM9B4d_JnRnL9oIyZcsIG793aOsRnuEA_aNpnuoPDyYA48MVNvnT16LGMrPQi5aV3yAddWKD-kFBeuJ24ByjoMIErRSFYPKXnTjwA-mbs0LIRBwzlfdWtr5roPyDARAXzUtfO3NzeDCG5uMO-xmgw5UL_ohL0rVeAfuPjwZZljNPZsLjwdmc0YTBB82v-fmybQKxBDtIhiz8JGLGrWtWhlS_bCh8hij0296zjMWA4yW-tz6gynu64xF_7khG4w4I1ZsQFgssoLPU-Rg2x943Aknub5NmKWoPaUjN6zcxVle1nNquqloiZ7XZh878da2xqbRTRfqxhTAOMFQHyoOMPIidFUihhNZxNC9lV5anHrjg7gHXRm7ESxzj2qHCJX9csnm1DntnZE72musAG0Tlfg4ylpyc33060RbJI8L_Aps_dOlSi2KTfKIwavutLoOoTJmRJDibzeX22Hs7HIQySnJdumWq7KtI_i0NSWHk0fo2ZiGInvuba83uDV049f3lSwV6CZz2v5vefLUCoMqQsBPDy0U' http://localhost:8001/api/user


 # Log

    error_log("---------------------------------------");
    error_log(print_r($args, true));
    \Log::info(auth()->guard('api')->user());



# cookie
▼Nuxt.jsでCookieを使って閲覧したデータを取得する方法
https://qiita.com/sauzar18/items/6eb3fe0218e3cf6badbc



# paginator

type Query {
    getLikeUsers(first: Int!, page: Int): UserPaginatorT @middleware(checks: ["auth:api"])
}

type UserPaginatorT {
    data: [Like!]!
    count: Int
}

public static function resolve()
{
    $query = Like::query();
    $query->where('liked_user_id',\Auth::user()->id);
    $like_user = $query->paginate(3);

    return [
        'data' => $like_user,
        // 'count'=> User::paginate(3)->count()
    ];
}



# S3登録

▼超簡単！LaravelでS3を利用する手順
https://qiita.com/tiwu_official/items/ecb115a92ebfebf6a92f

※手順で作成したkeyはcsvでダウンロードしてLaradockと同じ階層に残してる


    composer require league/flysystem-aws-s3-v3


## .env ファイル
    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=ap-northeast-1
    AWS_BUCKET=

※既にenvに定義されていると動かないので注意

#


# SVGにBase64エンコードしてCSSに直接埋め込む


base64 -i PixelMplus10-Regular.ttf -o output.txt

して出てきた文字列を

@font-face {
                font-family: 'PixelMplus';
                src: url("data:application/x-font-ttf; charset=utf-8; base64,〇〇〇
}
に入れる

▼svg要素をpngに変換する際に、webフォントが適用されない。
https://teratail.com/questions/112442

▼ [Webフォント] Base64エンコードしてCSSに直接埋め込む
https://www.cottpic.com/2018/10/embed-base64-encoded-fonts-in-css.html



# ssl化対応


▼初めてHerokuで独自ドメインを公開するあなたへ
https://qiita.com/kenjikatooo/items/07c3d911210a4ca96781

ここ見てやってたけどどうもアドオンを使わないといけないらしく上手くいかないため


ホスト名： www.twimon.com	
TYPE: CNAME	
TTL： 3600 
VALUE：tranquil-aptosaurus-5bsa5hmnouent780023yhwhh.herokudns.com

をお名前.comに登録するとhttps://www.twimon.comは上手く行くようになった

がルードドメインが上手くいかない・・・

いろいろ調べて行くうちに無料でできる方法が！！

▼【完全無料】Herokuで独自ドメイン + HTTPSに対応する【Rails】
https://qiita.com/serinuntius/items/f7f08b2221f5ad068f5d

Type: CNAME	
Name： twimon.com	
VALUE：https://twimon-frontend.herokuapp.com/
TTL： 3600 

を登録する。

そのあとお名前.comにてプライマリとセカンダリネームサーバーを更新すると・・・

https://twimon.com

で飛べるように！！！！！！！


