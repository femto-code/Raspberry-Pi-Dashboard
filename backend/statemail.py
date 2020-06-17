# -*- coding: utf-8 -*-
import subprocess
import smtplib
import socket
import datetime
import sys
from email.mime.text import MIMEText

receiver = 'YOUR_EMAIL_HERE'
sender = 'YOUR_EMAIL_HERE'
password = 'YOUR_PASSWORD_HERE'

smtpserver = smtplib.SMTP('YOUR_SMTP_SERVER_HERE', 587)
smtpserver.ehlo()
smtpserver.starttls()
smtpserver.ehlo

smtpserver.login(sender, password)

Datum = datetime.date.today()

Wert = str(sys.argv[1])
Wert += " "
Wert += str(sys.argv[2])
msg = MIMEText(Wert)

msg['Subject'] = 'Status Mail of RPi - %s' % Datum.strftime('%b %d %Y')

msg['From'] = sender

msg['To'] = receiver

# Send email
smtpserver.sendmail(sender, [receiver], msg.as_string())

smtpserver.quit()
