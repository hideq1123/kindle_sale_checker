<?php
require_once("/pear/Mail.php");//●●pearライブラリーのMail.phpまでのパスを設定してください。
require_once("/pear/Net/SMTP.php");//●●pearライブラリーのSMTP.phpまでのパスを設定してください。
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// Gmail送信に関する情報を入力
$params = array(
    "host" => "ssl://smtp.gmail.com",   // SMTPサーバー名(Gmailを想定しています)
    "port" => 465,              // ポート番号(Gmailを想定しています)
    "auth" => true,            // SMTP認証を使用する
    "username" => "",  // ●●送信元となるSMTPのユーザー名を設定してください。
    "password" => "", //●●送信元となるメールアドレスのパスワードを設定してください。
    "debug" => false,
    "protocol"=>"SMTP_AUTH"
);
$mailObject = Mail::factory("smtp", $params);
// Gmail送信に関する情報を入力ここまで


$url = array();
/*  下記にAmazonのURLを登録します。
 * PHP.iniのmax_execution_timeが30の時は5冊まで登録できます。60の時は10冊まで登録できます。
 * max_execution_timeの初期の数値は30のようです。
 * URLの直後にカタカナで「タイトル」と入れれば、本のタイトルを入力することができます。
 * 短縮URLでも使用可能です。あと、当然ですが、紙の本のほうのURLではなく、kindle版のURLを登録して下さい。
 */
$url = array("https://amzn.to/3chRBH4タイトル：人間達の話",
    "https://amzn.to/3aKZcgRタイトル：おもろい話し方",
    "https://amzn.to/3PDhf7Iタイトル：今日からつぶやけるひとりごと英語フレーズ",
    "https://amzn.to/3uVmXtwタイトル：スノウ・クラッシュ",
    "https://amzn.to/3aNk5YUタイトル：人間ぎらいのマーケティング",
    "https://amzn.to/3PCkHPAタイトル：世にも奇妙な君物語",
    "https://amzn.to/3aRDWWJタイトル：ランドスケープと夏の定理",
    "https://amzn.to/3ob9b2kタイトル：明日あるまじく候",
    "https://amzn.to/3ISuEXiタイトル：百年法",
    "https://amzn.to/3cp3JWVタイトル：ホテル・アルカディア"
);
$url_cnt = count($url);
$cnt = 0;

while($cnt < $url_cnt){
    $access_url = mb_strstr( $url[$cnt], 'タイトル', true);//「タイトル」以前を変数にセット
    $book_title = mb_strstr($url[$cnt], 'タイトル');//「タイトル」以降を変数にセット

    $html = file_get_contents($access_url);// スクレイピング

    //テキストファイルに書き込む処理
    $fh = fopen($dir."/kindle_check.txt", "w");
    fwrite($fh,$html);
    fclose($fh);
    //テキストファイルに書き込む処理ここまで


    $file = fopen($dir."/kindle_check.txt", "r");
    $campaign_class = "";
    if($file){//テキストファイルを1行ずつ読み込む
        while ($line = fgets($file)) {
            $campaign_class = 'class="limited-time-deal-badge-text">';//「期間限定キャンペーン」という文字のclass

            if(!strpos($line,$campaign_class) === false){//上記のclassがページ内にあった場合

                //メール送信処理
                $subject = "kindleキャンペーン始まった！";
                $mail = "";//●●送信先となるメールアドレスをここに設定してください。
                $message = $book_title ."のキャンペーンが始まったようです。";
                $headers = array(
                    "To" => $mail,
                    "From" => "",//●●送信元となるメールアドレスをここに設定してください。
                    "Subject" => mb_encode_mimeheader($subject) //日本語の件名を指定する場合、mb_encode_mimeheaderでエンコード
                );
                $message = mb_convert_encoding($message, "ISO-2022-JP", "UTF-8");// 日本語なのでエンコード
                $mailObject->send($mail, $headers, $message);//送信処理
                //メール送信処理ここまで
            }
        }
    }
    fclose($file);
    sleep(1);
    $cnt++;
}
//echo "fin";

?>
