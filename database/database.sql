DROP SCHEMA IF EXISTS lbaw2445 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw2445;

DROP DOMAIN IF EXISTS CurrentTime CASCADE;
DROP DOMAIN IF EXISTS FirstDay CASCADE;
DROP DOMAIN IF EXISTS Today CASCADE;

DROP TYPE IF EXISTS NotType CASCADE;
DROP TYPE IF EXISTS Styles CASCADE;
DROP TYPE IF EXISTS Themes CASCADE;
DROP TYPE IF EXISTS Techniques CASCADE;
DROP TYPE IF EXISTS TransactionType CASCADE;
DROP TYPE IF EXISTS ItemState CASCADE;
DROP TYPE IF EXISTS ReportType CASCADE;

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS images CASCADE;
DROP TABLE IF EXISTS items CASCADE;
DROP TABLE IF EXISTS bids CASCADE;
DROP TABLE IF EXISTS product_images CASCADE;
DROP TABLE IF EXISTS transactions CASCADE;
DROP TABLE IF EXISTS follows CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS artists CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS rates CASCADE;
DROP TABLE IF EXISTS chats CASCADE;
DROP TABLE IF EXISTS messages CASCADE;

DROP INDEX IF EXISTS user_items;
DROP INDEX IF EXISTS end_items;
DROP INDEX IF EXISTS user_transactions;
DROP INDEX IF EXISTS user_notifications;
DROP INDEX IF EXISTS item_search_idx;

DROP FUNCTION IF EXISTS item_search_update() CASCADE;	
DROP FUNCTION IF EXISTS prevent_admin_auction_activity() CASCADE;
DROP FUNCTION IF EXISTS prevent_auction_cancellation() CASCADE;
DROP FUNCTION IF EXISTS prevent_highest_bidder_rebid() CASCADE;
DROP FUNCTION IF EXISTS extend_auction_deadline() CASCADE;
DROP FUNCTION IF EXISTS prevent_seller_bidding() CASCADE;
DROP FUNCTION IF EXISTS check_auction_dates() CASCADE;
DROP FUNCTION IF EXISTS check_user_balance() CASCADE;


CREATE DOMAIN CurrentTime AS TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP;
CREATE DOMAIN FirstDay AS DATE CHECK (VALUE >= '0001-01-01');
CREATE DOMAIN Today AS DATE DEFAULT CURRENT_DATE;

CREATE TYPE NotType AS ENUM ('top_bidder', 'owner_soldItem', 'owner_notSold', 'endedgeneral' ,'ending5mingeneral', 'ending5minowner', 'Transaction', 'newbid', 'newbidowner', 'winner', 'canceled', 'suspended', 'unsuspended', 'canceledowner', 'suspendedowner', 'unsuspendedowner');

CREATE TYPE Styles AS ENUM (
    'Realism', 'Naturalism', 'Impressionism', 'Surrealism', 'Expressionism', 
    'Cubism', 'Abstract', 'Dadaism', 'Street Art', 'Minimalism',
    'Digital Painting', 'Drip Painting', 'Modernism', 'Romanticism', 'Classicism'
);

CREATE TYPE Themes AS ENUM (
    'Nature & Environment', 'Fantasy & Mythical', 'Emotions & Experiences', 
    'Urban & Social', 'Symbolism & Abstract', 'Space Exploration'
);

CREATE TYPE Techniques AS ENUM (
    'Brushwork', 'Glazing', 'Impasto', 'Color Mixing', 'Monochromatic Palette', 
    'Acrylic Pouring', 'Wet-on-wet', 'Dry Brush', 'Underpainting', 'Fresco', 
    'Layering', 'Oil on Canvas', 'Acrylic', 'Watercolor', 'Digital Art', 'Pastel',
    'Oil Painting', 'Charcoal Drawing', 'Ink and Brush'
);

CREATE TYPE TransactionType AS ENUM (
    'Selling', 'Buying', 'Deposit', 'Withdraw'
);

CREATE TYPE ItemState AS ENUM (
    'Pending', 'Auction', 'NotSold', 'Sold', 'Suspended'
);

CREATE TYPE ReportType AS ENUM (
    'Fraudulent Listing', 'Inappropriate Content', 'Violation of Rules', 'Copyright Infringement', 'Suspicious Activity', 'Other' 
);

CREATE TABLE images (
    imageId SERIAL PRIMARY KEY,
    imageURL VARCHAR(255) NOT NULL
);

CREATE TABLE users (  
    userId SERIAL PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
	isAdmin BOOLEAN NOT NULL,
	address VARCHAR(100),
    city VARCHAR(100),
    country VARCHAR(100),
    postalCode VARCHAR(20),
    phone VARCHAR(20),
    balance DECIMAL(10, 2),
    bidBalance DECIMAL (10, 2),
    imageId INT,
    birthDate FirstDay NOT NULL,
    rememberToken VARCHAR,
    banTime TIMESTAMP(0) NULL,
    bannedReason VARCHAR(50) NULL,
    resetCode VARCHAR(6), 
    resetCodeExpires TIMESTAMP(0) NULL,
	CONSTRAINT ck_users_birthDate CHECK (birthDate <= CURRENT_DATE - INTERVAL '18 YEARS'),
	FOREIGN KEY (imageId) REFERENCES images(imageId) ON DELETE SET NULL
);

CREATE TABLE artists (
    artistId SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    isFamous BOOLEAN NOT NULL
);

CREATE TABLE items (
    itemId SERIAL PRIMARY KEY,
    ownerId INT NOT NULL,
    topBidder INT,
	name VARCHAR(100) NOT NULL,
    initialPrice DECIMAL(10, 2) NOT NULL,
    soldPrice DECIMAL(10, 2),
    width DECIMAL(10, 2) NOT NULL,
    height DECIMAL(10, 2) NOT NULL,
    description TEXT,
	startTime CurrentTime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    duration INT NOT NULL DEFAULT 1440,
    deadline TIMESTAMP(0),
    style Styles NOT NULL,
    theme Themes NOT NULL,
    technique Techniques NOT NULL,
    artistId INT NOT NULL,
    state ItemState NOT NULL,
	CONSTRAINT ck_items_initialPrice CHECK (initialPrice > 0),
	CONSTRAINT ck_items_width CHECK (width > 0),
	CONSTRAINT ck_items_height CHECK (height > 0),
	FOREIGN KEY (ownerId) REFERENCES users(userId) ON DELETE SET NULL,
    FOREIGN KEY (topBidder) REFERENCES users(userId) ON DELETE SET NULL,	
	FOREIGN KEY (artistId) REFERENCES artists(artistId) ON DELETE SET NULL								
);

CREATE TABLE product_images (
    itemId SERIAL,
    imageId SERIAL,
    PRIMARY KEY (itemId, imageId),
	FOREIGN KEY (imageId) REFERENCES images(imageId) ON DELETE CASCADE,
	FOREIGN KEY (itemId) REFERENCES items(itemId) ON DELETE CASCADE
);

CREATE TABLE follows (
    followerId INT,
    itemId INT,
    PRIMARY KEY (followerId, itemId),
    FOREIGN KEY (followerId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (itemId) REFERENCES items(itemId) ON DELETE CASCADE
);

CREATE TABLE bids (
    bidId SERIAL PRIMARY KEY,
    bidderId INT NOT NULL,
	itemId INT NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    time CurrentTime NOT NULL,
	CONSTRAINT ck_bids_value CHECK (value > 0), 
	FOREIGN KEY (bidderId) REFERENCES users(userId) ON DELETE SET DEFAULT,
	FOREIGN KEY (itemId) REFERENCES items(itemId) ON DELETE CASCADE
);

CREATE TABLE transactions (
    transactionId SERIAL PRIMARY KEY,
    auctionId INT,
    userId INT NOT NULL,
    transactionType VARCHAR(50) NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    time CurrentTime NOT NULL,
	CONSTRAINT ck_transactions_value CHECK (value > 0),
	FOREIGN KEY (auctionId) REFERENCES items(itemId) ON DELETE CASCADE,
	FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
);

CREATE TABLE notifications (
    notificationId SERIAL PRIMARY KEY,
    userId INT NOT NULL,
    type NotType NOT NULL,
	bidId INT,
	itemId INT,
    itemName VARCHAR(50),
	transactionId INT,
    dateTime CurrentTime NOT NULL,
	FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE,
	FOREIGN KEY (bidId) REFERENCES bids(bidId) ON DELETE SET NULL,
	FOREIGN KEY (itemId) REFERENCES items(itemId) ON DELETE SET NULL,
	FOREIGN KEY (transactionId) REFERENCES transactions(transactionId) ON DELETE SET NULL
);

CREATE TABLE reports (
    reportId SERIAL PRIMARY KEY,
    reportedAuction INT NOT NULL,
    userId INT NOT NULL,
    type ReportType NOT NULL,
    reportText VARCHAR(255),
    reportTime TIMESTAMP(0) DEFAULT NOW(),
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (reportedAuction) REFERENCES items(itemId) ON DELETE CASCADE
);

CREATE TABLE rates (
    raterId INT,
    ratedId INT,
    rate DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (raterId, ratedId),
    FOREIGN KEY (raterId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (ratedId) REFERENCES users(userId) ON DELETE CASCADE
);

CREATE TABLE chats (
    chatId SERIAL PRIMARY KEY,
    userId INT NOT NULL,
    adminId INT,
    statusType VARCHAR(20) DEFAULT 'active',
    createdAt CurrentTime,
    updatedAt CurrentTime, 
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE,
    FOREIGN KEY (adminId) REFERENCES users(userId) ON DELETE CASCADE
);

CREATE TABLE messages (
    messageId SERIAL PRIMARY KEY,
    chatId INT NOT NULL,
    senderId INT NOT NULL,
    messageText TEXT NOT NULL,
    createdAt CurrentTime,
    FOREIGN KEY (chatId) REFERENCES chats(chatId) ON DELETE CASCADE,
    FOREIGN KEY (senderId) REFERENCES users(userId) ON DELETE CASCADE
);

--Indexes--
CREATE INDEX user_items ON items USING hash (ownerId);
CREATE INDEX end_items ON items USING btree (deadline);
CREATE INDEX user_transactions ON transactions USING hash (userId);
CREATE INDEX user_notifications ON notifications USING hash (userId);

ALTER TABLE items 
ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION item_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors := 
            setweight(to_tsvector('english', NEW.name), 'A') ||
            setweight(to_tsvector('english', NEW.description), 'B');
    ELSIF TG_OP = 'UPDATE' THEN
        IF NEW.name <> OLD.name OR NEW.artistId <> OLD.artistId THEN
            NEW.tsvectors := 
                setweight(to_tsvector('english', NEW.name), 'A') ||
                setweight(to_tsvector('english', NEW.description), 'B');
        END IF;
    END IF;
    RETURN NEW;
END
$$ LANGUAGE plpgsql;

CREATE TRIGGER item_search_update
BEFORE INSERT OR UPDATE ON items
FOR EACH ROW
EXECUTE PROCEDURE item_search_update();

CREATE INDEX item_search_idx ON items USING GIN (tsvectors);



--Triggers--
CREATE OR REPLACE FUNCTION prevent_admin_auction_activity() RETURNS TRIGGER AS $$
BEGIN
    -- Check if the user is an admin and prevent them from participating in auctions
    IF EXISTS (SELECT 1 FROM users WHERE userId = NEW.bidderId AND isAdmin = TRUE) THEN
        RAISE EXCEPTION 'Administrators cannot participate in or create auctions';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_admin_bid 
BEFORE INSERT ON bids 
FOR EACH ROW 
EXECUTE FUNCTION prevent_admin_auction_activity();

CREATE OR REPLACE FUNCTION prevent_auction_cancellation() RETURNS TRIGGER AS $$
BEGIN
    -- Check if there are any bids associated with the auction item
    IF EXISTS (SELECT 1 FROM bids WHERE itemId = OLD.itemId) THEN
        RAISE EXCEPTION 'Cannot cancel an auction with confirmed bids';
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_cancellation 
BEFORE DELETE ON items 
FOR EACH ROW 
EXECUTE FUNCTION prevent_auction_cancellation();


CREATE OR REPLACE FUNCTION prevent_highest_bidder_rebid() RETURNS TRIGGER AS $$
BEGIN
    -- Prevent a user from placing a bid if they are already the highest bidder for the item
    IF NEW.bidderId = (SELECT bidderId FROM bids WHERE itemId = NEW.itemId ORDER BY value DESC LIMIT 1) THEN
        RAISE EXCEPTION 'You cannot place a bid if you are already the highest bidder';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_rebid 
BEFORE INSERT ON bids 
FOR EACH ROW 
EXECUTE FUNCTION prevent_highest_bidder_rebid();


CREATE OR REPLACE FUNCTION extend_auction_deadline() RETURNS TRIGGER AS $$
BEGIN
    -- Extend the auction deadline by 30 minutes if a bid is placed within 15 minutes of the current deadline
    IF NEW.time >= (SELECT deadline - INTERVAL '15 minutes' FROM items WHERE itemId = NEW.itemId) THEN
        UPDATE items 
        SET deadline = deadline + INTERVAL '30 minutes' 
        WHERE itemId = NEW.itemId;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER extend_deadline_trigger 
AFTER INSERT ON bids 
FOR EACH ROW 
EXECUTE FUNCTION extend_auction_deadline();


CREATE OR REPLACE FUNCTION check_user_balance() RETURNS TRIGGER AS $$
BEGIN
    -- Check if the user has enough balance to place the bid
    IF (SELECT balance FROM users WHERE userId = NEW.bidderId) < NEW.value THEN
        RAISE EXCEPTION 'Insufficient balance to place this bid';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_balance_before_bid 
BEFORE INSERT ON bids 
FOR EACH ROW 
EXECUTE FUNCTION check_user_balance();

CREATE OR REPLACE FUNCTION prevent_seller_bidding() RETURNS TRIGGER AS $$
BEGIN
    -- Prevent the seller from placing a bid on their own auction item
    IF NEW.bidderId = (SELECT ownerId FROM items WHERE itemId = NEW.itemId) THEN
        RAISE EXCEPTION 'Sellers cannot bid on their own auction items';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER prevent_self_bid 
BEFORE INSERT ON bids 
FOR EACH ROW 
EXECUTE FUNCTION prevent_seller_bidding();

CREATE OR REPLACE FUNCTION check_auction_dates() RETURNS TRIGGER AS $$
BEGIN
    -- Ensure the auction end date is greater than the start date
    IF NEW.deadline <= NEW.startTime THEN
        RAISE EXCEPTION 'The auction end date must be greater than the start date';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER validate_auction_dates 
BEFORE INSERT OR UPDATE ON items 
FOR EACH ROW 
EXECUTE FUNCTION check_auction_dates();

CREATE OR REPLACE FUNCTION handle_user_deletion() RETURNS TRIGGER AS $$
BEGIN
    -- Update the ownerId to 0 (or an "Unknown" user ID) for items owned by the deleted user
    UPDATE items
    SET ownerId = 0
    WHERE ownerId = OLD.userId;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_items_on_user_delete
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION handle_user_deletion();

CREATE OR REPLACE FUNCTION handle_bidder_deletion() RETURNS TRIGGER AS $$
BEGIN
    -- Update the bidderId to 0 (or an "Unknown" user ID) for bids placed by the deleted user
    UPDATE bids
    SET bidderId = 0
    WHERE bidderId = OLD.userId;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_bids_on_user_delete
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION handle_bidder_deletion();

CREATE OR REPLACE FUNCTION update_timestamp() RETURNS TRIGGER AS $$
BEGIN
    NEW.updatedAt = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_timestamp_trigger
BEFORE UPDATE ON chats
FOR EACH ROW
EXECUTE FUNCTION update_timestamp();

