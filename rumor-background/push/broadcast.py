# coding: UTF-8
import sys
import pprint

sys.path.append('/home/nishimura/.local/lib/python2.7/site-packages')
#pprint.pprint(sys.path)

from linebot import LineBotApi
from linebot.models import TextSendMessage

f = open('/home/nishimura/public_html/rumor-bot/conf/lineAccessToken.txt')
LINE_CHANNEL_ACCESS_TOKEN = f.read()  # ファイル終端まで全て読んだデータを返す
f.close()

line_bot_api = LineBotApi(LINE_CHANNEL_ACCESS_TOKEN)

#user_id = "Uf811de50a7725a63c181cf7fc8977ae7"

messages = TextSendMessage(text="http://web.wakayama-u.ac.jp/~yoshino/lab/")
#line_bot_api.push_message(user_id, messages=messages) #id指定して個人に送信するパターン
#line_bot_api.multicast(['to1', 'to2'], TextSendMessage(text='Hello World!')) #複数ユーザに送信するには配列でidを渡す
line_bot_api.broadcast(TextSendMessage(text='ブロードキャスとのテストです．http://web.wakayama-u.ac.jp/~yoshino/lab/')) #登録者全員に送信
print("送信完了！")
