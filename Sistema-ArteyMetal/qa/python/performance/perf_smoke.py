import os
import re
import statistics
import time
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor
from http.cookiejar import CookieJar


BASE_URL = os.getenv("BASE_URL", "http://127.0.0.1:8000").rstrip("/")
LOGIN_USER = os.getenv("LOGIN_USER", "bvasquezkeysije")
LOGIN_PASSWORD = os.getenv("LOGIN_PASSWORD", "76636255")
TARGETS = [
    "/login",
    "/productos/create",
]


def build_opener():
    jar = CookieJar()
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))


def get_csrf_token(opener):
    with opener.open(f"{BASE_URL}/login", timeout=20) as response:
        html = response.read().decode("utf-8", errors="ignore")
    match = re.search(r'name="_token"\s+value="([^"]+)"', html)
    if not match:
        raise RuntimeError("No se encontro token CSRF en login.")
    return match.group(1)


def login(opener):
    token = get_csrf_token(opener)
    payload = urllib.parse.urlencode(
        {
            "_token": token,
            "login": LOGIN_USER,
            "password": LOGIN_PASSWORD,
        }
    ).encode()
    request = urllib.request.Request(
        f"{BASE_URL}/login",
        data=payload,
        headers={"Content-Type": "application/x-www-form-urlencoded"},
        method="POST",
    )
    with opener.open(request, timeout=20) as response:
        response.read()


def fetch_authenticated(path):
    opener = build_opener()
    start = time.perf_counter()
    try:
        login(opener)
        with opener.open(f"{BASE_URL}{path}", timeout=20) as response:
            status = response.status
            response.read()
    except Exception:
        status = 0
    elapsed = (time.perf_counter() - start) * 1000
    return {"path": path, "status": status, "ms": round(elapsed, 2)}


def run_round(concurrency=3, rounds=5):
    jobs = TARGETS * rounds
    with ThreadPoolExecutor(max_workers=concurrency) as executor:
        return list(executor.map(fetch_authenticated, jobs))


def summarize(results):
    times = [item["ms"] for item in results]
    success = [item for item in results if item["status"] == 200]
    print("Total requests:", len(results))
    print("Successful:", len(success))
    print("Average ms:", round(statistics.mean(times), 2))
    print("Median ms:", round(statistics.median(times), 2))
    print("Max ms:", max(times))
    print("Min ms:", min(times))
    for path in TARGETS:
        subset = [item["ms"] for item in results if item["path"] == path]
        if subset:
            print(f"{path} avg ms:", round(statistics.mean(subset), 2))


if __name__ == "__main__":
    summarize(run_round())
