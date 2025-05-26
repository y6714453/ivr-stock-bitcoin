import edge_tts
import asyncio
import subprocess
import requests
from requests_toolbelt.multipart.encoder import MultipartEncoder
import os

# 🟡 טוקן של ימות (אפשר גם לשים כמשתנה סביבה במקום בקובץ)
token = "xh8jRwCtZEqR4jxb"

# 🔄 שליפת טקסט מעודכן מה-API שלך
def get_bitcoin_text():
    try:
        response = requests.get(
            "https://ivr-stock-bitcoin-production-2a11.up.railway.app/test.php?symbol=BTC-USD",
            timeout=10
        )
        response.raise_for_status()
        text = response.text.strip()
        print("📥 טקסט התקבל מה-API:")
        print(text)
        return text
    except Exception as e:
        print("❌ שגיאה בשליפת הנתונים מה-API:", e)
        return "הביטקוין עומד כעת על נתון לא זמין."

# 🟢 שמות הקבצים
mp3_file = "btc_temp.mp3"
wav_file = "M0000.wav"
destination_path = 'ivr2:/8/M0000.wav'

# 🎙 יצירת קובץ MP3
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

# 📤 העלאה לימות
def upload_to_yemot():
    print("📤 מעלה לימות...")
    try:
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
    except Exception as e:
        print("❌ שגיאה בהעלאה לימות:", e)

# 🧠 הרצה
async def main():
    text = get_bitcoin_text()
    await create_mp3(text)
    convert_to_wav()
    upload_to_yemot()

asyncio.run(main())
