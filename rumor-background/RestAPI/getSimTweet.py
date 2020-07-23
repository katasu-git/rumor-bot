# coding: UTF-8
import sys
import MeCab
import json

def getStopWords():
    #http://svn.sourceforge.jp/svnroot/slothlib/CSharp/Version1/SlothLib/NLP/Filter/StopWord/word/Japanese.txt
    f = open('/home/nishimura/public_html/rumor-bot/rumor-background/stopwordJP.txt')
    data = f.read()  # ファイル終端まで全て読んだデータを返す
    f.close()

    data = data.splitlines()
    stopWords = []
    for d in data:
        if d != "":
            stopWords.append( d )

    return stopWords

def returnAnaText(text):
    #辞書の場所を指定する（なぜかフルパスで指定しないと動かない）
    userdic_path="-d /usr/local/lib/mecab/dic/mecab-ipadic-neologd" #空白削除しないと精度でない？
    m = MeCab.Tagger("-Ochasen")

    nouns = [line.split()[0] for line in m.parse(text).splitlines()
                if "名詞" in line.split()[-1] and not(line.split()[0] in getStopWords()) and not(len(line.split()[0]) == 1)]
                #if "名詞" in line.split()[-1] and not(line.split()[0] in getStopWords())] #配列の最後が「名詞」の時 かつ　ストップワードでない場合

    return nouns

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

def textsToUni(texts):
    uniTexts = []
    for t in texts:
        uniTexts.append( unicode(t, 'utf-8') )
    return uniTexts

def dupCounter(l1, l2):
    l1_l2_and = set(l1) & set(l2)
    #print(l1_l2_and)
    return l1_l2_and

def getWakachi(rumors):
    wakachiList = []
    for r in rumors:
        if r != ['']:
            wakachiList.append( r[3].split('/') )
    return wakachiList

def createHitKeywords(keywords):
    slashKeywords = ""
    for k in keywords:
        slashKeywords = slashKeywords + k + "/"
    slashKeywords = slashKeywords[:-1]
    return slashKeywords

def getSimRumor(wakachiList, nouns):
    hitList = []
    for i in range( len(wakachiList) ):
        keywords = dupCounter(wakachiList[i], nouns)
        length = len(keywords)
        if length > 0:
            slashKeywords = createHitKeywords(keywords) # ヒットしたキーワードをスラッシュ区切りで取り出す
            dic = [length, int(i), slashKeywords]
            hitList.append( dic )
    return hitList

def main():
    nouns = returnAnaText(sys.argv[1])
    """
    for n in nouns:
        print n
    """
    rumors = getRumorsJson()
    wakachiList = getWakachi(rumors)
    hitList = getSimRumor(wakachiList, nouns)
    hitList = sorted(hitList, reverse=True, key=lambda x: x[0])  #lengthに注目してソート

    del hitList[3:]

    if hitList:
        for h in hitList:
            rumors[h[1]].append(h[2]) # どのキーワードにヒットっしたかを加える
            print(rumors[h[1]])

    """
    if not hitList:
        print("なさそうだね。他にも何か試してみる？")
    else:
        for h in hitList:
            print(rumors[h[1]][1])
    """

main()