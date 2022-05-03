use challenge;

#All messages in any conversation sent by the user with user ID 4
SELECT * FROM message WHERE userId = 4;

#All messages in conversation where users 1 and 3 are participating (other users could also be participating)
SELECT DISTINCT message.* FROM message INNER JOIN conversation ON message.conversationId = conversation.id WHERE conversation.userId = 1 OR conversation.userId = 3;

#All messages in any conversation where the message contents include the word "cake"
SELECT * FROM message WHERE txt LIKE '%cake%';

