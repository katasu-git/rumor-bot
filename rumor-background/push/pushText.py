# coding: UTF-8
import sys
import pprint
import json
import copy
import datetime

sys.path.append('/home/nishimura/.local/lib/python2.7/site-packages')
#pprint.pprint(sys.path)

from linebot import LineBotApi
from linebot.models import TextSendMessage
from linebot.models import FlexSendMessage

def getRumorsJson():
    #json_open = open('/home/nishimura/public_html/editTsv/rumor_kakimoto.json', 'r')
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/rumor-nishimura.txt')
    #id　訂正数　元の流言テキスト　分かち書きの結果（スラッシュ区切り）
    data1 = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()
    line = data1.splitlines()
    rumors = []
    for l in line:
        if l.split('	') != ['']: #これがないとゴミが入る
            rumors.append( l.split('	') )
    return rumors


f = open('/home/nishimura/public_html/rumor-bot/conf/lineAccessToken.txt')
LINE_CHANNEL_ACCESS_TOKEN = f.read()  # ファイル終端まで全て読んだデータを返す
f.close()

line_bot_api = LineBotApi(LINE_CHANNEL_ACCESS_TOKEN)

rumors = getRumorsJson()

dt_now = datetime.datetime.now()
if(dt_now.hour < 12):
    rumors = rumors[:5]
    message = TextSendMessage(text="どうも、ちるもです\uDBC0\uDCA9\n今日も怪しい情報がたくさん出回っているよ\uDBC0\uDC9B\n気になる情報があったら僕に話しかけてみてね！")
else:
    rumors = rumors[5:9]
    message = TextSendMessage(text="今日も一日お疲れ様！\uDBC0\uDC86\n今夜も怪しい情報には注意してね\uDBC0\uDC29\n気になる情報があったら僕に話しかけてみてね！")

json_open = open('./flex.json', 'r')
flextemp = json.load(json_open)

flex_array = []
for r in rumors:
    flexmessage = flextemp
    flexmessage["header"]["contents"][0]["text"] = r[1] # ヘッダーに流言本体を挿入
    flexmessage["body"]["contents"][0]["contents"][0]["text"] = "この情報を" + str(r[2]) + "人が疑っています" # 訂正人数を挿入
    flexmessage["footer"]["contents"][0]["action"]["uri"] = r[4] # URLを挿入
    flex_array.append(copy.deepcopy(flexmessage))

#print(flex_array)

carouselJSON = {
    "type": "carousel",
    "contents": flex_array
}

flex_message = FlexSendMessage(
    alt_text='Flex Message',
    contents=carouselJSON
)

line_bot_api.push_message(user_id, messages=[flex_message,message]) #id指定して個人に送信するパターン
#line_bot_api.multicast(['to1', 'to2'], messages) #複数ユーザに送信するには配列でidを渡す
#line_bot_api.broadcast(messages=[flex_message,message]) #登録者全員に送信
print('送信完了！')
