// Dữ liệu mẫu cho bảng levels
INSERT INTO levels (name, description) VALUES 
('Pre-Starter', 'Dành cho người mới bắt đầu hoàn toàn, làm quen với bảng chữ cái và phát âm cơ bản.'),
('Starters', 'Tiếng Anh thiếu nhi cấp độ 1: Từ vựng về màu sắc, con vật, gia đình và chào hỏi đơn giản.'),
('Movers', 'Tiếng Anh thiếu nhi cấp độ 2: Xây dựng câu đơn giản, nghe hiểu hội thoại ngắn hàng ngày.'),
('Flyers', 'Tiếng Anh thiếu nhi cấp độ 3: Đọc hiểu văn bản ngắn, viết đoạn văn miêu tả cơ bản.'),
('KET (A2)', 'Trình độ sơ cấp: Có thể giao tiếp trong các tình huống quen thuộc và đơn giản.'),
('PET (B1)', 'Trình độ trung cấp: Sử dụng tiếng Anh độc lập trong công việc, học tập và du lịch.'),
('IELTS 0 - 3.0', 'Xây dựng nền tảng: Lấy lại gốc ngữ pháp và từ vựng cho người mất căn bản.'),
('IELTS 3.0 - 5.0', 'Pre-IELTS: Làm quen với các dạng bài thi và củng cố 4 kỹ năng Nghe-Nói-Đọc-Viết.'),
('IELTS 5.0 - 6.5', 'IELTS Master: Luyện đề chuyên sâu và nâng cao kỹ năng tư duy phản biện.'),
('Giao tiếp căn bản', 'Luyện phản xạ nghe nói cho người đi làm, tập trung vào các chủ đề văn phòng.');

// Dữ liệu mẫu cho bảng vocabularies
INSERT INTO vocabularies (level_id, word, meaning, type, example_sentence, image_url) VALUES 

-- =============================================
-- LEVEL 1: PRE-STARTER (Từ vựng cơ bản nhất)
-- =============================================
(1, 'Sun', 'Mặt trời', 'flashcard', 'The sun rises in the east.', 'uploads/sun.png'),
(1, 'Moon', 'Mặt trăng', 'flashcard', 'The moon is beautiful tonight.', 'uploads/moon.png'),
(1, 'Star', 'Ngôi sao', 'flashcard', 'Look at that bright star.', NULL),
(1, 'Water', 'Nước', 'mixed', 'I drink water every day.', 'uploads/water.jpg'),
(1, 'Bread', 'Bánh mì', 'fill_gap', 'I eat bread for breakfast.', NULL),
(1, 'Milk', 'Sữa', 'flashcard', 'Baby likes to drink milk.', NULL),
(1, 'Book', 'Quyển sách', 'mixed', 'Open your book, please.', NULL),
(1, 'Pen', 'Cái bút', 'fill_gap', 'Can I borrow your pen?', NULL),
(1, 'School', 'Trường học', 'flashcard', 'I go to school by bus.', NULL),
(1, 'Teacher', 'Giáo viên', 'mixed', 'My teacher is very kind.', NULL),
(1, 'Cat', 'Con mèo', 'uploads/cat.png', NULL, 'flashcard', 'The cat is sleeping.'),
(1, 'Dog', 'Con chó', NULL, NULL, 'fill_gap', 'The ___ barks loudly.'), 
(1, 'Apple', 'Quả táo', NULL, NULL, 'mixed', 'I like to eat an apple every day.'),
(1, 'Ball', 'Quả bóng', NULL, NULL, 'flashcard', 'He plays with a ball.'),
(1, 'Car', 'Xe hơi', NULL, NULL, 'fill_gap', 'My father drives a ___.'),
(1, 'House', 'Ngôi nhà', NULL, NULL, 'mixed', 'They live in a big house.'),
(1, 'Tree', 'Cây xanh', NULL, NULL, 'flashcard', 'The tree is tall.'),
(1, 'Chair', 'Cái ghế', NULL, NULL, 'fill_gap', 'Please sit on the ___.'),
(1, 'Table', 'Cái bàn', NULL, NULL, 'mixed', 'The book is on the table.'),
(1, 'Fish', 'Con cá', NULL, NULL, 'flashcard', 'I have a pet fish.'),

-- =============================================
-- LEVEL 2: STARTERS (Màu sắc, cảm xúc, gia đình)
-- =============================================
(2, 'Happy', 'Vui vẻ / Hạnh phúc', 'flashcard', 'She looks very happy.', 'uploads/happy.png'),
(2, 'Sad', 'Buồn bã', 'fill_gap', 'Why are you so ___ today?', NULL), -- Key: sad
(2, 'Angry', 'Tức giận', 'mixed', 'Don\'t be angry with me.', NULL),
(2, 'Green', 'Màu xanh lá', 'flashcard', 'The grass is green.', NULL),
(2, 'Yellow', 'Màu vàng', 'flashcard', 'Bananas are yellow.', NULL),
(2, 'Red', 'Màu đỏ', 'mixed', 'She is wearing a red dress.', NULL),
(2, 'Brother', 'Anh/Em trai', 'fill_gap', 'My ___ is taller than me.', NULL), -- Key: brother
(2, 'Sister', 'Chị/Em gái', 'mixed', 'I have two sisters.', NULL),
(2, 'Grandmother', 'Bà', 'flashcard', 'My grandmother is 80 years old.', NULL),
(2, 'Family', 'Gia đình', 'fill_gap', 'I love my ___ very much.', NULL), -- Key: family
(2, 'Blue', 'Màu xanh dương', NULL, NULL, 'flashcard', 'The sky is blue.'),
(2, 'Mother', 'Mẹ', 'uploads/mother.png', NULL, 'fill_gap', 'My ___ cooks very well.'), -- Từ cần điền là Mother
(2, 'Father', 'Bố', NULL, NULL, 'mixed', 'I play football with my father.'),

-- =============================================
-- LEVEL 3: MOVERS (Động từ, hoạt động hàng ngày)
-- =============================================
(3, 'Breakfast', 'Bữa sáng', 'flashcard', 'What did you have for breakfast?', NULL),
(3, 'Lunch', 'Bữa trưa', 'mixed', 'We eat lunch at 12 PM.', NULL),
(3, 'Dinner', 'Bữa tối', 'fill_gap', 'My mom is cooking ___.', NULL), -- Key: dinner
(3, 'Sleep', 'Ngủ', 'flashcard', 'I go to sleep at 10 PM.', NULL),
(3, 'Watch', 'Xem (TV)', 'mixed', 'They watch TV every evening.', NULL),
(3, 'Listen', 'Nghe', 'fill_gap', 'Please ___ to the music.', NULL), -- Key: listen
(3, 'Play', 'Chơi', 'flashcard', 'Let\'s play football.', NULL),
(3, 'Cook', 'Nấu ăn', 'mixed', 'Can you cook spaghetti?', NULL),
(3, 'Drive', 'Lái xe', 'fill_gap', 'He knows how to ___ a car.', NULL), -- Key: drive
(3, 'Swim', 'Bơi lội', 'flashcard', 'I like to swim in the sea.', NULL),

-- =============================================
-- LEVEL 7: IELTS 0 - 3.0 (Học thuật sơ cấp)
-- =============================================
(7, 'Education', 'Giáo dục', 'mixed', 'Education is important for everyone.', NULL),
(7, 'Global', 'Toàn cầu', 'flashcard', 'English is a global language.', NULL),
(7, 'Topic', 'Chủ đề', 'fill_gap', 'Today\'s ___ is environment.', NULL), -- Key: topic
(7, 'Discuss', 'Thảo luận', 'mixed', 'We need to discuss this problem.', NULL),
(7, 'Solution', 'Giải pháp', 'fill_gap', 'We must find a ___ quickly.', NULL), -- Key: solution
(7, 'Reason', 'Lý do', 'flashcard', 'Give me a reason to stay.', NULL),
(7, 'Opinion', 'Ý kiến', 'mixed', 'In my opinion, it is good.', NULL),
(7, 'Agree', 'Đồng ý', 'fill_gap', 'I totally ___ with you.', NULL), -- Key: agree
(7, 'Disagree', 'Không đồng ý', 'flashcard', 'It is okay to disagree.', NULL),
(7, 'Improve', 'Cải thiện', 'mixed', 'I want to improve my skills.', NULL),
(7, 'Student', 'Sinh viên / Học sinh', NULL, NULL, 'flashcard', 'Every student needs a book.'),
(7, 'Library', 'Thư viện', 'uploads/library.jpg', NULL, 'fill_gap', 'We study in the ___ every afternoon.'),
(7, 'University', 'Trường đại học', NULL, NULL, 'mixed', 'She wants to go to a top university.'),
(7, 'Homework', 'Bài tập về nhà', NULL, NULL, 'fill_gap', 'I must finish my ___ before watching TV.'),

-- =============================================
-- LEVEL 10: GIAO TIẾP VĂN PHÒNG (Business English)
-- =============================================
(10, 'Deadline', 'Hạn chót', 'fill_gap', 'We must meet the ___ tomorrow.', NULL), -- Key: deadline
(10, 'Schedule', 'Lịch trình', 'mixed', 'Let me check my schedule.', NULL),
(10, 'Presentation', 'Bài thuyết trình', 'flashcard', 'Your presentation was excellent.', NULL),
(10, 'Report', 'Báo cáo', 'fill_gap', 'Submit the monthly ___ by Friday.', NULL), -- Key: report
(10, 'Client', 'Khách hàng', 'mixed', 'The client is waiting in the lobby.', NULL),
(10, 'Manager', 'Quản lý', 'flashcard', 'I need to speak to the manager.', NULL),
(10, 'Promotion', 'Thăng chức', 'fill_gap', 'She got a ___ last month.', NULL), -- Key: promotion
(10, 'Resign', 'Từ chức', 'mixed', 'He decided to resign for personal reasons.', NULL),
(10, 'Recruit', 'Tuyển dụng', 'flashcard', 'Our company wants to recruit new staff.', NULL),
(10, 'Invoice', 'Hóa đơn', 'fill_gap', 'Please send me the ___ via email.', NULL); -- Key: invoice
(10, 'Meeting', 'Cuộc họp', NULL, NULL, 'flashcard', 'We have a meeting at 9 AM.'),
(10, 'Contract', 'Hợp đồng', 'uploads/contract.png', NULL, 'fill_gap', 'Please sign the ___ on the last page.'),
(10, 'Salary', 'Tiền lương', NULL, NULL, 'mixed', 'He receives a high salary.'),
(10, 'Colleague', 'Đồng nghiệp', NULL, NULL, 'flashcard', 'My colleague sits next to me.');



