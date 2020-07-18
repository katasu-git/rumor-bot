from kutt import kutt 


def getShortURL(longURL):
    f = open('/home/nishimura/public_html/rumor-bot/conf/kutt.txt')
    API = f.read()  # ファイル終端まで全て読んだデータを返す
    obj = kutt.submit(API, longURL, reuse=True) # reuse, customurl and password are OPTIONAL
    shortURL = obj['data']['link']
    return shortURL