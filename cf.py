import os
import sys
import time
import json
import warnings
from seledroid import webdriver
from seledroid.webdriver.common.by import By
#from seledroid import webdriver

driver = webdriver.Chrome()
host = sys.argv[1]

def Cloudflare():
    title = driver.title
    if any(sub.lower() in title.lower() for sub in ["cloudflare", "just a moment..."]):
        time.sleep(10)
        return False
    else:
        return True

try:
    driver.get(host)
    while not Cloudflare():
        time.sleep(3)

    cf_clearance = driver.get_cookie("cf_clearance")
    user_agent = driver.user_agent
    print(f"CF BYPASSED COOKIE: {cf_clearance}")
    print(f"NEW USERAGENT: {user_agent}")

except Exception as e:
    data = {"error": str(e)}
finally:
    title = driver.title
    if any(sub.lower() in title.lower() for sub in ["cloudflare", "just a moment..."]):
        data = {
            "cf_clearance": False,
            "user-agent": user_agent,
        }
    else:
        data = {
            "cf_clearance": cf_clearance.split("=")[1],
            "user-agent": user_agent,
        }

    print(json.dumps(data))  # Output the data as JSON

    # Use close() instead of quit()
    try:
        driver.close()
    except Exception:
        pass
