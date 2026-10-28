import os
import time

import pymysql
from flask import Flask, redirect, render_template, request, url_for

app = Flask(__name__)


def get_connection():
    return pymysql.connect(
        host=os.environ.get("DB_HOST", "db"),
        port=int(os.environ.get("DB_PORT", "3306")),
        user=os.environ.get("DB_USER", "mural_user"),
        password=os.environ.get("DB_PASSWORD", ""),
        database=os.environ.get("DB_NAME", "mural"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )


def initialize_database():
    # O MariaDB pode demorar alguns segundos para inicializar na primeira subida.
    last_error = None
    for _ in range(30):
        try:
            connection = get_connection()
            try:
                with connection.cursor() as cursor:
                    cursor.execute(
                        """
                        CREATE TABLE IF NOT EXISTS mensagens (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            nome VARCHAR(80) NOT NULL,
                            mensagem VARCHAR(500) NOT NULL,
                            criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )
                        """
                    )
            finally:
                connection.close()
            return
        except pymysql.MySQLError as error:
            last_error = error
            time.sleep(2)

    raise RuntimeError(f"Não foi possível conectar ao MariaDB: {last_error}")


@app.get("/")
def index():
    connection = get_connection()
    try:
        with connection.cursor() as cursor:
            cursor.execute(
                "SELECT id, nome, mensagem, criada_em "
                "FROM mensagens ORDER BY id DESC"
            )
            mensagens = cursor.fetchall()
    finally:
        connection.close()

    return render_template("index.html", mensagens=mensagens)


@app.post("/mensagens")
def adicionar_mensagem():
    nome = request.form.get("nome", "").strip()
    mensagem = request.form.get("mensagem", "").strip()

    if nome and mensagem:
        connection = get_connection()
        try:
            with connection.cursor() as cursor:
                cursor.execute(
                    "INSERT INTO mensagens (nome, mensagem) VALUES (%s, %s)",
                    (nome[:80], mensagem[:500]),
                )
        finally:
            connection.close()

    return redirect(url_for("index"))


if __name__ == "__main__":
    initialize_database()
    app.run(host="0.0.0.0", port=5000)

