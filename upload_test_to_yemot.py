import requests

token = 'dJkO28JNKPlS6a9E'
destination_path = 'ivr2:/08/M0000.mp3'
file_path = 'test.mp3'

with open(file_path, 'rb') as f:
    response = requests.post(
        'https://www.call2all.co.il/ym/api/UploadFile',
        params={'token': token, 'what': destination_path},
        files={'file': f}
    )

if response.status_code == 200 and 'OK' in response.text:
    print("✅ הקובץ הועלה בהצלחה לשלוחה 8")
else:
    print("❌ שגיאה בהעלאה:")
    print(response.text)
