# SimpleAsteriskContact2XML

Asterisk/FreePBXのContactをXMLに吐き出すツール(現在、GrandStreamのみ対応)

## サマリー

　FreePBX(Asterisk)のContact Manager（連絡先）で作成したグループ・連絡先をXMLに書き出し、GRANDSTREAM社のIP電話機に取り込むためのシンプルなPHPプログラムです。

## 使い方

　FreePBXのwwwルートの適当なところに設置してください。例えば・・・

> http://freepbx.local/pb/contact2xml.php

という感じです。


### 次節以降のように、URL引数を渡す場合の注意点

URL引数を渡す場合、最後に&EOFを付けてください。付けない場合は、正常に動作しません。これは、GRANDSTREAM電話機が、URLに/phonebook.xmlと自動で付与するためです。当システムでは、ファイル名独自指定は機能的に予約しているため、特段対処は起きないません。

URL引数を渡す場合、最後に&EOFを付けてください。


### 毎回データを取得する場合(強制取得))

初期状態では、30分に一回程度データを取得し、XMLファイルとしてキャッシュします。しかし、それが上手くいかない場合や、常に最新の情報を取得したい場合は、は次のようにしてください。

強制的にデータを取得する場合は

> http://freepbx.local/pb/contact2xml.php?FORCE=1&EOF

としてください。

### 表示データの取得について

初期状態では、ディスプレイネームを取得表示しています。これを姓・名で取得表示するには、

> http://freepbx.local/pb/contact2xml.php?MODE1=1&EOF

とするか、定義ファイルのMODE1を1にしてください。

> define("MODE1",1);
>
> ( by contact2xml.conf)

### GrandStream GXP1xxx /  GXP2xxx と GVX3xxx の設定について

GrandStream GXP1xxx /  GXP2xxxは

TYPE=gs

GVX3xxxは

TYPE=gs3000

とURLに渡してください。具体的には、

> http://freepbx.local/pb/contact2xml.php?FORCE=1&TYPE=gs3000&EOF

となります。

### 取得する連絡先グループの指定の方法

GPID[]=1

などURLに指定してください。複数指定することが出来ます。そのときは、

GPID[]=1&GPID[]=6

というようになります。

具体的には、

> http://freepbx.local/pb/contact2xml.php?FORCE=1&TYPE=gs3000&GPID[]=1&GPID[]=6&EOF

となります。

### 内線番号とSIP-LINE IDが混在する環境で、連絡先グループにデフォルト内線番号(SIP-LINE ID)を割り当てる方法

連絡先グループのコード5番をSIP-LINE ID 1に割り当てる場合は、

ACID[5]=1

等と指定してください。これにより、設定ファイルの内容より優先して適用されます。

具体的には、

> http://freepbx.local/pb/contact2xml.php?FORCE=1&TYPE=gs3000&GPID[]=1&GPID[]=6&ACID[5]=1&EOF

となります。


### 利用者が変更するところ

contact2xml.confファイルの次の行を適宜変更してください。

> FETCH_TIME = 30;

取得時間。初期値は30分。詳細は後述の注意を読んでください。

> FILENAME = "phonebook.xml"

XMLの一時記録ファイル名。

| $DEFAULT_ACCOUNTIDX = 1;

　ACCOUNTIDXを設定しない場合、どのSIP内線番号に割り当てるかを指定します。1始まり。

> ACCOUNTIDX[x] = y;

accountindexに対応する部分です。添え字([]の中のx)には、グループIDを指定し、代入するxはSIP回線(1-6)を指定します。グループIDは「連絡先の管理」のグループ名にマウスカーソルをのせると「〜#EXTERNAL-4」とか数字が表示されます。この数字を指定してください。

### 注意

　このプログラムは、生成したXMLをファイルとして保存します。初期状態では、30分以内の取り合わせに対しては、ファイルのXMLを返します。それを超えた場合、またはFORCE引数を与えた場合はAsteriskのデータベースから取得してXMLを返します。

　混在環境の場合は、件数が多い取得をキャッシュ利用するか、ディレクトリを切って複数設置するなど対応してください。リクエスト数が少ない場合は、引数で制御してもさほど問題は無いかと思います。

## 制限事項・注意事項

- 初期状態で取得するXMLのContact情報の姓は、Contact Managerのディスプレイ名です。
- プライベート電話帳については全く関知していません。テストもしていません。

## 謝辞

このプログラムは、次の記事を元に作成いたしました。心より感謝致します。

Phonebook store location - General Help - FreePBX Community Forums  https://community.freepbx.org/t/phonebook-store-location/36062/13
