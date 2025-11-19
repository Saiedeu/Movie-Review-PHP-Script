ALTER TABLE reviews 
ADD COLUMN download_movie_url TEXT DEFAULT NULL,
ADD COLUMN download_subtitle_url TEXT DEFAULT NULL;