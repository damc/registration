# Installation and usage

In order to start the project, run:

```symfony server:start```

You have to have symfony binary installed which you can do by running:

```wget https://get.symfony.com/cli/installer -O - | bash```

You can find more information in the documentation of Symfony 5.

# Possible performance optimizations

1. One of the requirement is that the user should be able to return to the step at which they ended when returning to registration, so the data that they already inserted must be saved. I decided to store that in session. It could be also saved to the database with flag enabled=false, but still we need store the information in session (or cookie) about which record is associated to that user. One of the possible performance optimization would be to change the way sessions are stored. Currently, they are stored in files. I expect that databases are optimized for fast retrieval, so I expect that storing them in database would be faster. I expect they would be the fastest if they were stored in Redis or Memcached database, rather than MySQL or other relational database because Redis and Memcache use hash table to store their data and hash table is faster on average than a table not using hashes to get the needed value (even if the field by which we search is indexed).

2. Creating an index on ``email`` column in the ``user`` table will allow to validate faster if there is already a user with that email.

3. There are some redirections made, e.g. when the user gets back to the registration, then they are redirected to the step that they ended at. Those redirections could be avoided and that would give small performance improvement. They can be avoided for example by having one action for each step and a parameter 'step' instead of few actions (each action for each step), but that would hurt readability.

# Which things could be done better

1. In the ``RegistrationController``, there are few actions - each action is responsible for one step of the registration. Some of those actions are quite similar, what is different is usually the form and what is done after submitting that form. This duplication could be removed by creating a private method registrationStep which would take the form as an argument and a callable representing what should be done when the form is submitted. However, I believe that the code is more readable and easier to maintain as it is. Sometimes forcing DRY principle results in spending more time than necessary on understanding the code and making changes. Usually following DRY principle results in the opposite, but there are exceptions, and I consider it to be that exception. That being said, I extracted some similar parts to separate methods - ``redirectToPreviousStepIfIncompleteData`` method is an example of that. Also, the view template is shared by all steps.

2. I could use JS to make the form steps working without reloading the page.

3. The method ``savePaymentData`` uses ``HttpClient`` to send a request to retrieve the paymentDataId and then saves it in the user. I think creating one more level of abstraction for the client could be useful, if the project would continue to be developed in the future. I would create class ``PaymentApiClient`` that would have ``sendPaymentData`` method which would be responsible for sending the request and returning the paymentDataID. ``savePaymentData`` would use that method. This could be potentially beneficial in the future because if there was another part in the project that needs to send that request (without saving it in the payment details), it could reuse the logic for handling the request.

4. Handling exceptions. Instead of allowing them to crash the application, I could catch them, log them and display an error to the user.

5. In order to display paymentDataID, I needed to save them somewhere. I am saving it in the User table, but in order to know which user's paymentDataId I need to display, I need to store that information somehow. For that reason I store the PaymentDataID in session. However, ideally the user should be authenticated after registration and redirected to a page that displays PaymentDataID for the currently authenticated user. I haven't done that because authentication functionality is not implemented in this project.

6. The session is removed in ``success()`` action and not after saving the row to the database because I need to display paymentDataId in the last step. This creates the problem that if you add the record, but something is wrong after that, then the session is not removed and then it will redirect you to that step again and if you try to add that user again, it will inform you that the user with that email already exists. The possible solution is: don't store paymentDataID in session and remove the session after adding the user; store userId in the session instead; later retrieve the paymentDataId from the database, not the session. Another possible solution is to add authentication as described above and dispaly paymentDataId of the currently authenticated user.

7. Unit testing could be added.
