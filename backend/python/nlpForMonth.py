

import time
import json
import collections #配列の要素カウント
from datetime import datetime as dt
from datetime import timezone,timedelta

from base import connectDBClass as database

from nlp import special_people_extract
from nlp import classification_analysis
from nlp import importantWords_analysis
from nlp import emotions_analysis
from nlp import causeEffect_analysis
from nlp import dependency_analysis
from nlp import cosSimilarity_analysis

from nlp.dic import dic_to_trie

def nlpForMonth(user_id):

    #DBインスタンス
    db = database.connectDB()

    #タイムゾーン
    JST = timezone(timedelta(hours=+9), 'JST')


    # SELECT id,updated_at,updated_statistic_at,date,sentence,chunk,token,affiliation,char_length,token
    rows=db.get_all_diariesNlpFin_from_user(user_id)


    #月ごとを書くのする辞書型配列
    yMonth_dicList={}

    '''
    月ごとの空の配列作成処理
    '''
    for row in rows:
        value_date=str(row[3])
        date=value_date.split('-')
        # 辞書のラベル用
        date_label=date[0]+"-"+date[1]

        # 月ごとに分岐した空の辞書を作る
        yMonth_dicList[date_label]={
            'emotions':[],
            'word_counts':[],
            'noun_rank':[],
            'adjective_rank':[],
            'important_words':[],
            'special_people':[],
            'classifications':[],
        }
    print("空の配列作成完了")



    
    #その月のデータが既にあったら更新しない
    


    '''
    配列を先に作る
    nullの処理が面倒すぎるので。
    '''
    
    '''
    統計更新してから日記側に変更がないとき(updated_statistic_at<=updated_at)→処理しない分岐
    dbに入っている日付2021-09-20 14:29:16

    id,updated_at,updated_statistic_at,date,char_length,emotions,classification,important_words,special_people
    '''
    for row in rows:
        
        #個別日記のループ
        # if(row[1]!=None):
        #      time_updated_at = row[1]#この時点でdatetime型になっている
        # else:
        #     # データない場合
        #     time_updated_at = time.strptime('1800-1-1 11:11:11', '%Y-%m-%d %H:%M:%S')
        # #統計の更新日取得
        # if(row[2]!=None):
        #     time_statistics_updated_at = row[2]
        # else:
        #     # データない場合
        #     time_statistics_updated_at = dt.strptime('1800-1-1 11:11:11','%Y-%m-%d %H:%M:%S')
        #本当は条件分岐したいが、全部まとめてやったほうが早いので、分岐せず数の暴力でぶん回す
        if(0):
            #処理不要 リーダーブルコードに乗ってたやつ
            print(str(row[0])+"スキップ")
            continue
        else:
            print(str(row[0])+"Diary処理")
            # nlp関係はNoneがあるので注
            #jsonはdecodeする
            value_id=row[0]
            value_updated_at=row[1]
            value_updated_statistic_at=row[2]
            value_date=str(row[3])
            value_char_length=row[4]
            value_emotions=row[5]
            value_classification=row[6]
            value_important_words=json.loads(row[7])
            value_special_people=json.loads(row[8])
            value_token=json.loads(row[9])

            '''
            年月日に分ける
            '''
            date=value_date.split('-')
            # 辞書のラベル用
            date_label=date[0]+"-"+date[1]
            day=date[2]

            '''
            感情まとめ
            emotions
            {
            day:
            value:
            }
            '''
            yMonth_dicList[date_label]['emotions'].append({   
                "day":day,
                "value":value_emotions,
            })
            #足すだけなので処理不要


            '''
            文字数まとめ
            word_counts
            {
            day:
            count:
            }
            '''
            yMonth_dicList[date_label]['word_counts'].append({   
                "day":day,
                "value":value_char_length,
            })


            '''
            名詞多い順3
            noun_rank
            {
            name:
            count:
            }
            
            形容詞多い順3
            adjective_rank
            {
            name:
            count:
            }

            '''


            #token複数あるので、ループで処理
            for individual_token in value_token.values():
                #残り
                '''
                dict
                {
                    'lemma':#基本形
                    'xPOSTag':#言語依存の品詞(動詞-一般的な)
                }
                '''
                if("名詞" in individual_token['xPOSTag'] ):
                    yMonth_dicList[date_label]['noun_rank'].append(individual_token['lemma'])
                elif("形容詞" in individual_token['xPOSTag']):
                    yMonth_dicList[date_label]['adjective_rank'].append(individual_token['lemma'])


            '''
            重要そうな単語3
            important_words
            {
            name:
            count:
            }
            '''
            # for important_word in value_important_words.values():
            #     #残り
            #     yMonth_dicList[date_label]['important_words'].append(important_word)

            '''
            人物多い順3
            special_people
            {
            name:
            count:
            }
            '''


            '''
            推定分類3つ
            special_people
            {
            name:
            count:
            }
            '''


               
    #forループここまで

    '''
    年月日に分けたものを整形する処理
    '''
    for yMonth_dic in yMonth_dicList.values():
        '''
        名詞
        '''
        noun_rank_raw=collections.Counter(yMonth_dic['noun_rank'])#単語要素別にカウント
        noun_rank_all = sorted(noun_rank_raw.items(), key=lambda x:x[1])#値の大きい順にソート
        noun_rank=noun_rank_all[0:10]#上位10個まで
        #代入
        # print(noun_rank)    
        yMonth_dic['noun_rank']=noun_rank
        '''
        形容詞
        '''
        adjective_rank_raw=collections.Counter(yMonth_dic['adjective_rank'])#単語要素別にカウント
        adjective_rank_all = sorted(adjective_rank_raw.items(), key=lambda x:x[1])#値の大きい順にソート
        adjective_rank=adjective_rank_all[0:10]#上位10個まで
        #代入
        # print(adjective_rank)    
        yMonth_dic['adjective_rank']=adjective_rank
    # print( yMonth_dicList)


    # print( yMonth_dicList)
        
    '''
    DB更新
    '''

    #更新日反映(これは不要→updated_atと統計が同義なので)
    #進捗反映
    #月と年生成
    #DB代入(無い場合insert、あるときupdate)
    #まだ　meta_info,emotions,flavor,similar_sentences,classification,important_words,cause_effect_sentences,special_people,updated_statistic_at
    # db.set_single_json_data('diaries',row[0],important_words=important_words,special_people=special_people)
    # db.set_single_normal_data('diaries',row[0],classification=classification,emotions=emotions,updated_statistic_at=updated_statistic_at)

    # db.set_single_progress(row[0],"diaries",100)

    db.set_multiple_progress(user_id,"statistics",40)
    del db

    print("nlpForDiary終了")

if __name__ == '__main__':
    nlpForMonth(2)