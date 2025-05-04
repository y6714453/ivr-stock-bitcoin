from flask import Flask

app = Flask(__name__)

@app.route("/bitcoin")
def bitcoin():
    try:
        with open("btc.txt", "r", encoding="utf-8") as f:
            return f.read()
    except:
        return "שגיאה בקריאת מחיר הביטקוין."

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=8080)
