import edge_tts
import asyncio

async def main():
    tts = edge_tts.Communicate("בדיקת מערכת. זהו קובץ נסיון.", "he-IL-AvriNeural")
    await tts.save("test.mp3")
    print("✅ נוצר הקובץ test.mp3")

asyncio.run(main())
