# Description

This is a simple PHP application with HTML, CSS, JavaScript for the frontend part.
In this application user can subscribe for Comics which will be mailed to every subscribed user every 5 minutes. User can subscribe by registration on the website will verify the mail address and then send emails. In the Email there will be comic image with title of the comic and link to unsubscribe. For the unsubscribe user will have to verify his email with OTP.

# Problems Occured and Their Solutions

Many Problems occured in which the one which used up the most time was sending mail without any library support. Finally that problem was solved using curl command in PHP with SendGrid API. Also another problem which occured was regarding attaching the same image as attachment and adding it as inline in mail too. This problem was solved by using content_id provided by SendGrid API for attachments.
But all together I learned a lot from this assignment.

# Live Demo Link

https://xkcd-project.netlify.app/
