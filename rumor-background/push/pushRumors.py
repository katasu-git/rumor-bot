# coding: UTF-8
import sys
sys.path.append('/home/nishimura/.local/lib/python2.7/site-packages')
import json
import copy
from linebot import LineBotApi
from linebot.models import TextSendMessage
from linebot.models import FlexSendMessage

def createFlexMessage(rumors, rumorType):
    json_open = open('./flex_v2.json', 'r')
    flextemp = json.load(json_open)
    flex_array = []

    for r in rumors:
        flexmessage = flextemp

        content = r['content']
        URL = "https://liff.line.me/1654776413-dpYy83Wb/?id=" + str(r['id']) + "&path=detail"
        if rumorType == "today":
            label = "【今日の新着】"
            fixText = "疑っている人：" + str(r['fix']) + "人"
            topColor = "#A0D7DC"
        elif rumorType == "attention":
            label = "【注目・急上昇】"
            fixText = "疑っている人：" + str(r['fix']) + "人（昨日より）"
            topColor = "#EF7943"
        elif rumorType == "ranking":
            label = "【訂正数ランク】"
            fixText = "疑っている人：" + str(r['fix']) + "人"
            topColor = "#74BE89"

        flexmessage["header"]["contents"][0]["text"] = label
        flexmessage["header"]["contents"][1]["text"] = fixText
        flexmessage["body"]["contents"][0]["contents"][1]["text"] = content
        flexmessage["footer"]["contents"][0]["action"]["uri"] = URL
        flexmessage["styles"]["header"]["backgroundColor"] = topColor
        flex_array.append(copy.deepcopy(flexmessage))

    carouselJSON = {
        "type": "carousel",
        "contents": flex_array
    }

    flex_message = FlexSendMessage(
        alt_text='Flex Message',
        contents=carouselJSON
    )
    return flex_message

def sendMessage(messagesToSend):
    f = open('/home/nishimura/public_html/rumor-bot/conf/lineAccessToken.txt')
    LINE_CHANNEL_ACCESS_TOKEN = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()
    line_bot_api = LineBotApi(LINE_CHANNEL_ACCESS_TOKEN)
    line_bot_api.push_message("Uf811de50a7725a63c181cf7fc8977ae7", messages=messagesToSend) #id指定して個人に送信するパターン
    # line_bot_api.multicast(['to1', 'to2'], messages) #複数ユーザに送信するには配列でidを渡す
    # line_bot_api.broadcast(messages=[flex_message,message]) #登録者全員に送信

def pushRumors(rumors):
    messagesToSend = createFlexMessage(rumors, "today")
    sendMessage(messagesToSend)