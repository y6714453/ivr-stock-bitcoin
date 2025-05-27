import edge_tts
import asyncio
import subprocess
import requests
from requests_toolbelt.multipart.encoder import MultipartEncoder
import os
import time

# 🟡 טוקן חדש של ימות
token = 'NiNHWEKrCzhENYnN'

# 🔄 שליפת נתוני ביטקוין מ-Yahoo Finance
def get_bitcoin_text():
    try:
        url = "https://query1.finance.yahoo.com/v8/finance/chart/BTC-USD?range=6mo&interval=1d"
        headers = {'User-Agent': 'Mozilla/5.0'}
        response = requests.get(url, headers=headers, timeout=10)
        data = response.json()['chart']['result'][0]

        current_price = data['meta']['regularMarketPrice']
        year_high = data['meta'].get('fiftyTwoWeekHigh', None)
        timestamps = data['timestamp']
        prices = data['indicators']['quote'][0]['close']

        now = time.time()
        def closest_price(target):
            for t, p in zip(reversed(timestamps), reversed(prices)):
                if t <= target and p is not None:
                    return p
            return None

        start_of_day = time.mktime(time.localtime(now)[:3] + (0, 0, 0, 0, 0, -1))
        start_of_week = now - (time.localtime(now).tm_wday * 86400)
        start_of_year = time.mktime(time.strptime(f"{time.localtime().tm_year}-01-01", "%Y-%m-%d"))

        price_day = closest_price(start_of_day)
        price_week = closest_price(start_of_week)
        price_year = closest_price(start_of_year)

        def format_change(current, previous):
            if previous is None or previous == 0:
                return "אין נתון זמין"
            change = ((current - previous) / previous) * 100
            sign = "עלייה" if change > 0 else "ירידה" if change < 0 else "שינוי אפסי"
            abs_change = abs(change)
            change_text = "אחוז" if round(abs_change, 2) == 1.00 else f"{abs_change:.2f}".replace(".", " נקודה ") + " אחוז"
            return f"{sign} של {change_text}"

        def spell_price(p):
            p = round(p)
            th = p // 1000
            r = p % 1000
            if th == 0:
                return f"{r}"
            elif th == 1:
                return f"אלף ו{r}" if r else "אלף"
            elif th == 2:
                return f"אלפיים ו{r}" if r else "אלפיים"
            else:
                return f"{th} אלף ו{r}" if r else f"{th} אלף"

        price_txt = spell_price(current_price)
        change_day = format_change(current_price, price_day)
        change_week = format_change(current_price, price_week)
        change_year = format_change(current_price, price_year)

        dist_txt = ""
        if year_high:
            diff = ((current_price - year_high) / year_high) * 100
            abs_diff = abs(diff)
            dist_txt = f"{abs_diff:.2f}".replace(".", " נקודה ") + " אחוז"

        text = (
            f"הָבִּיטְקוֹיְן עומד כעת על {price_txt} דולר. "
            f"מאז תחילת היום נרשמה {change_day}. "
            f"מתחילת השבוע נרשמה {change_week}. "
            f"בשלושת החודשים האחרונים נרשמה {change_year}. "
            f"המחיר הנוכחי רחוק מהשיא ב{dist_txt}."
        )
        return text

    except Exception as e:
        print("❌ שגיאה בשליפת נתונים:", e)
        return "הביטקוין עומד כעת על נתון לא זמין."

# 🟢 שמות הקבצים
mp3_file = "btc_temp.mp3"
wav_file = "M0000.wav"
destination_path = 'ivr2:/8/M0000.wav'

# 🎙 יצירת MP3
async def create_mp3(text):
    print("🎙️ מייצר MP3...")
    tts = edge_tts.Communicate(text, "he-IL-AvriNeural")
    await tts.save(mp3_file)
    print("✅ נוצר קובץ MP3")

# 🎛 המרה ל-WAV
def convert_to_wav():
    print("🔁 ממיר ל-WAV...")
    subprocess.run([
        "ffmpeg",
        "-y",
        "-i", mp3_file,
        "-ac", "1",
        "-ar", "8000",
        "-sample_fmt", "s16",
        wav_file
    ])
    print(f"✅ מוכן: {wav_file}")

# 📤 העלאה לימות המשיח
def upload_to_yemot():
    print("📤 מעלה לימות...")
    m = MultipartEncoder(
        fields={
            'token': token,
            'path': destination_path,
            'upload': (wav_file, open(wav_file, 'rb'), 'audio/wav')
        }
    )
    response = requests.post(
        'https://www.call2all.co.il/ym/api/UploadFile',
        data=m,
        headers={'Content-Type': m.content_type}
    )
    if response.status_code == 200 and 'OK' in response.text:
        print("✅ הועלה בהצלחה לשלוחה 8!")
    else:
        print("❌ שגיאה בהעלאה:")
        print(response.text)

# 🧠 הרצה
async def main():
    text = get_bitcoin_text()
    print("📝 טקסט להקראה:", text)
    await create_mp3(text)
    convert_to_wav()
    upload_to_yemot()

asyncio.run(main())
