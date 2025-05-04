import requests
import pandas as pd
import time
from datetime import datetime, timedelta

def get_price_history():
    url = "https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1d&limit=365"
    response = requests.get(url)
    data = response.json()
    df = pd.DataFrame(data, columns=[
        'Open time', 'Open', 'High', 'Low', 'Close', 'Volume',
        'Close time', 'Quote asset volume', 'Number of trades',
        'Taker buy base asset volume', 'Taker buy quote asset volume', 'Ignore'
    ])
    df['Close'] = df['Close'].astype(float)
    df['Open'] = df['Open'].astype(float)
    return df

def get_percentage_change(current, previous):
    if previous == 0:
        return 0
    return round(((current - previous) / previous) * 100, 2)

def build_report():
    df = get_price_history()
    now_price = round(df['Close'].iloc[-1], 0)
    today_open = round(df['Open'].iloc[-1], 0)
    week_start = df['Close'].iloc[-7]
    month_start = df['Close'].iloc[-30]
    year_start = df['Close'].iloc[0]
    max_price = round(df['Close'].max(), 0)

    day_change = get_percentage_change(now_price, today_open)
    week_change = get_percentage_change(now_price, week_start)
    month_change = get_percentage_change(now_price, month_start)
    year_change = get_percentage_change(now_price, year_start)
    from_high = get_percentage_change(now_price, max_price)

    direction = lambda x: "עלה" if x >= 0 else "ירד"
    abs_pct = lambda x: abs(x)

    report = (
        f"הביטקוין שווה כעת {now_price} דולר.\n"
        f"מאז פתיחת היום, הוא {direction(day_change)} ב־{abs_pct(day_change)} אחוז.\n"
        f"מאז תחילת השבוע, הוא {direction(week_change)} ב־{abs_pct(week_change)} אחוז.\n"
        f"מאז תחילת החודש, הוא {direction(month_change)} ב־{abs_pct(month_change)} אחוז.\n"
        f"מאז תחילת השנה, הוא {direction(year_change)} ב־{abs_pct(year_change)} אחוז.\n"
        f"כעת, הביטקוין נמצא במרחק של {abs_pct(from_high)} אחוז מהשיא השנתי שלו."
    )

    return report

def run_loop():
    while True:
        try:
            report = build_report()
            with open("btc.txt", "w", encoding="utf-8") as f:
                f.write(report)
            print("✓ נכתב btc.txt בהצלחה")
        except Exception as e:
            print("שגיאה:", e)
        time.sleep(5)

if __name__ == "__main__":
    run_loop()
