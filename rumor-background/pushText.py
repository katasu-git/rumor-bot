# coding: UTF-8
import sys
import pprint

sys.path.append('/home/nishimura/.local/lib/python2.7/site-packages')
#pprint.pprint(sys.path)

from linebot import LineBotApi
from linebot.models import TextSendMessage

LINE_CHANNEL_ACCESS_TOKEN = "wsF8qpwm05QsP47KzulqQDidI5l3RgIZvYvX6kZcjLuwPWnhXJGAHRM6g/WPwUZVXvOZUb8UNl9ApEn6UhYpJL9X/s8Zh/P2BqRNxXH0gfcdxVf+Fm68NRh5GIltjQxj8CuD8lJPBOBlM4j207b/XgdB04t89/1O/w1cDnyilFU="

line_bot_api = LineBotApi(LINE_CHANNEL_ACCESS_TOKEN)

user_id = "Uf811de50a7725a63c181cf7fc8977ae7"

messages = TextSendMessage(text="こんにちは！")
line_bot_api.push_message(user_id, messages=messages)
