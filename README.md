# SimpleAsteriskContact2XML

Asterisk/FreePBXのContactをXMLに吐き出すツール(GrandStreamのみ対応)

## サマリー

　FreePBX(Asterisk)のContact Manager（連絡先）で作成したグループ・連絡先をXMLに書き出し、GRANDSTREAM社のIP電話機に取り込むためのシンプルなPHPプログラムです。

## 使い方

　FreePBXのwwwルートの適当なところに設置してください。例えば・・・

> http://freepbx.local/pb/contact2xml.php

という感じです。強制的にデータを取得する場合は

> http://freepbx.local/pb/contact2xml.php?FORCE=1

としてください。

### 利用者が変更するところ

contact2xml.confファイルの次の行を適宜変更してください。

> FETCH_TIME = 30;

取得時間。初期値は30分。詳細は後述の注意を読んでください。

> FILENAME = "phonebook.xml"

XMLの一時記録ファイル名。

> ACCOUNTIDX[x] = y;

accountindexに対応する部分です。添え字([]の中のx)には、グループIDを指定し、代入するxはSIP回線(1-6)を指定します。グループIDは「連絡先の管理」のグループ名にマウスカーソルをのせると「〜#EXTERNAL-4」とか数字が表示されます。この数字を指定してください。

### 注意

　このプログラムは、生成したXMLをファイルとして保存します。初期状態では、30分以内の取り合わせに対しては、ファイルのXMLを返します。それを超えた場合、またはFORCE引数を与えた場合はAsteriskのデータベースから取得してXMLを返します。

## 制限事項・注意事項

- 取得するXMLのContact情報の姓は、Contact Managerのディスプレイ名です。姓・名は取得していません。
- 電話番号1件につき、連絡先1件となります。

## 謝辞

このプログラムは、次の記事を元に作成いたしました。心より感謝致します。

Phonebook store location - General Help - FreePBX Community Forums  https://community.freepbx.org/t/phonebook-store-location/36062/13
