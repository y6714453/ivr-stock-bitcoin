import requests
import edge_tts
import asyncio

# === הגדרות קבועות ===
token = 'dJkO28JNKPlS6a9E'
destination_path = 'ivr2:/08/M0000.mp3'
voice = "he-IL-AvriNeural"  # קול גברי בעברית
output_file = "btc.mp3"

# === שליפת מחיר ביטקוין מאתר Yahoo ===
def get_bitcoin_price():
    url = "https://query1.finance.yahoo.com/v8/finance/chart/BTC-USD?range=1d&interval=1m"
    response = requests.get(url)
    data = response.json()
    price = data["chart"]["result"][0]["meta"]["regularMarketPrice"]
    return round(price)

# === יצירת טקסט לדיבור ===
def generate_text(price):
    return f"הביטקוין עומד כעת על {price} דולר. זהו עדכון אוטומטי משרת הבורסה."

# === יצירת קובץ MP3 עם edge-tts ===
async def make_mp3(text):
    tts = edge_tts.Communicate(text, voice)
    await tts.save(output_file)
    print("✅ נוצר קובץ MP3")

# === העלאת הקובץ לימות ===
def upload_to_yemot():
    with open(output_file, 'rb') as f:
        response = requests.post(
            'https://www.call2all.co.il/ym/api/UploadFile',
            params={'token': token, 'what': destination_path},
            files={'file': f}
        )
        if response.status_code == 200 and 'OK' in response.text:
            print("✅ הקובץ הועלה לשלוחה בימות המשיח")
        else:
            print("❌ שגיאה בהעלאה:")
            print(response.text)

# === הרצה כוללת ===
async def main():
    price = get_bitcoin_price()
    text = generate_text(price)
    await make_mp3(text)
    upload_to_yemot()

asyncio.run(main())
