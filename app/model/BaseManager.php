<?php

namespace App\Model;

use Nette;

class BaseManager
{
    const
        TABLE_USER = 'user',
        USER_COLUMN_ID = 'iduser',
        USER_COLUMN_NAME = 'name',
        USER_COLUMN_SURNAME = 'surname',
        USER_COLUMN_LOGIN = 'login',
        USER_COLUMN_PASSWORD_HASH = 'password';

    const
        TABLE_TEST = 'test',
		TEST_ID = 'idtest',
		TEST_NAME = 'name',
		TEST_TIME = 'time',
		TEST_DIFFICULTY = 'difficulty';

    const
        TABLE_QUESTION = 'question',
		QUESTION_ID = 'idquestion',
		QUESTION_ID_TEST = 'test_idtest',
		QUESTION_QUESTION = 'question',
		QUESTION_TYPE = 'type',
		QUESTION_A = 'A',
		QUESTION_B = 'B',
		QUESTION_C = 'C',
		QUESTION_D = 'D',
		QUESTION_E = 'E',
		QUESTION_F = 'F',
		QUESTION_RIGHT_ANSWER = 'right_answer';

    const TABLE_ARTICLE = 'article',
		ARTICLE_ID_ARTICLE = 'idarticle',
		ARTICLE_NAME = 'name',
		ARTICLE_IMG_URL = 'img_url',
		ARTICLE_SONG_URL = 'song_url',
		ARTICLE_WEIGHT_MIN = 'weight_min',
		ARTICLE_WEIGHT_MAX = 'weight_max',
		ARTICLE_HEIGHT_MIN = 'height_min',
		ARTICLE_HEIGHT_MAX = 'height_max',
		ARTICLE_ORDER = 'order',
		ARTICLE_GRAPH_URL = 'graph_url',
		ARTICLE_ARTICLE = 'article';

    const TABLE_TEST_HAS_ARTICLE = 'test_has_article',
		TEST_HAS_ARTICLE_ID_TEST = 'test_idtest',
		TEST_HAS_ARTICLE_ID_ARTICLE = 'article_idarticle';

    const TABLE_USER_DONE_TEST = 'user_done_test',
		USER_DONE_TEST_ID_USER = 'user_iduser',
		USER_DONE_TEST_ID_TEST = 'test_idtest',
		USER_DONE_TEST_REMAIN_TIME = 'remain_time',
		USER_DONE_TEST_DATE = 'date';

    const TABLE_DONE_QUESTION = 'done_question',
		DONE_QUESTION_ID_QUESTION = 'question_idquestion',
		DONE_QUESTION_ID_USER = 'user_done_test_user_iduser',
		DONE_QUESTION_ID_TEST = 'user_done_test_test_idtest',
		DONE_QUESTION_ANSWERED = 'answered',
		DONE_QUESTION_ANSWER = 'answer',
		DONE_QUESTION_OLD = 'old';
}