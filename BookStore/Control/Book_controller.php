<?php
include_once '../Model/Book.php';
include_once '../Model/Category.php';
include_once '../Model/Order.php';
include_once '../Model/Manager.php';
include_once '../Model/BookOrder.php';
include_once 'Category_controller.php';
include_once 'DBConnection.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Book_controller
 *
 * @author Christian
 */
class Book_controller {
    private $conn;
    public function __construct() {
        $this->conn= new DBConnection();
    }
    
    public function getAllBooks()
    {
        $sql = "SELECT id FROM book";
        $books = new ArrayObject();
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc())
        {
            $id = $row['id'];
            $book = $this->getBookByID($id);
            $books->append($book);
        }
        return $books;
    }
         
    public function deleteBookCategory(Category $category, Book $book)
    {
        $sql = "DELETE FROM book_category WHERE category_id = $category->id AND book_id = $book->id";
        return $this->conn->statement($sql);
    }
    
    public function addCategoryToBook(Category $category, Book $book)
    {
        $sql = "INSERT INTO book_category(book_id, category_id) VALUES($book->id, $category->id)";
        return $this->conn->statement($sql);
    }
    /**
     * 
     * @return Book $book
     * @param Book $book
     */
    public function getCategoriesForBook(Book $book)
    {
        $sql = "SELECT * FROM book_category WHERE book_id = $book->id";
        $result = $this->conn->query($sql);
        $category_ctr = new Category_controller();
        $categories = new ArrayObject();
        while ($row = $result->fetch_assoc())
        {
            $category_id = $row['category_id'];
            $category = new Category();
            $category = $category_ctr->loadCategory($category_id);
            $categories->append($category);
        }
        return $categories;
    }

    public function getBookByID($id)
    {
        $sql = "SELECT * FROM Book WHERE id = $id";
        $book = new Book();
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc())
        {
            $book->id = $row['id'];
            $book->title = $row['title'];
            $book->author = $row['author'];
            $book->genre = $row['genre'];
            $book->price = $row['price'];
            $book->description = $row['description'];
            $book->inventory = $row['inventory'];
            $book->discount = $row['discount'];
            $book->picture = $row['picture'];
            $book->added = $row['added'];
        }
        return $book;
    } 
    
    public function createBook(Book $book)
    {
        $sql = "INSERT INTO `book` (`title`, `author`, `genre`, `price`, `inventory`, `description`, `added`, `discount`, `picture`) VALUES ('$book->title', '$book->author', '$book->genre', '$book->price', '$book->inventory', '$book->description', NOW(), '$book->discount', '$book->picture');";
        return $this->conn->statementReturnID($sql);
    }
    
    public function deleteBook(Book $book)
    {
        $sql = "DELETE FROM Book WHERE id = $book->id";
        return $this->conn->statement($sql);
    }
    
    public function updateBook(Book $book)
    {
        $sql = "UPDATE `book` SET `title`='$book->title', `author`='$book->author', `genre`='$book->genre', `price`='$book->price', `inventory`='$book->inventory', `description`='$book->description', `discount`='$book->discount', `picture`='$book->picture' WHERE `id`='$book->id';";
        return $this->conn->statement($sql);
    }  
    
    public function getNewestBooks() 
    {
        $sql = "SELECT id FROM book ORDER BY added DESC LIMIT 5";
        $books = new ArrayObject();
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc())
        {
            $id = $row['id'];
            $book = $this->getBookByID($id);
            $books->append($book);
        }
        return $books;
    }
    
    public function getSpecialOfferBooks() 
    {
        $sql = "SELECT id FROM book ORDER BY discount DESC LIMIT 5";
        $books = new ArrayObject();
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc())
        {
            $id = $row['id'];
            $book = $this->getBookByID($id);
            $books->append($book);
        }
        return $books;
    }
    
    public function getMostOrderedBooks() 
    {
        $sql = "SELECT id FROM book INNER JOIN order_book ON book.id = order_book.book_id GROUP BY book.id ORDER BY count(*) DESC LIMIT 5";
        $books = new ArrayObject();
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc())
        {
            $id = $row['id'];
            $book = $this->getBookByID($id);
            $books->append($book);
        }
        return $books;
    }
    
    public function searchBooks(string $query)
    {
        $words = explode(' ', $query);
        $authors_where = implode ("%' OR author LIKE '%", $words);  
        $titles_where = implode ("%' OR title LIKE '%", $words);

        $sql = "SELECT id FROM book WHERE author LIKE '%$authors_where%' OR title LIKE '%$titles_where%'";
        $result = $this->conn->query($sql);     
        $books = new ArrayObject();
        while ($row = $result->fetch_assoc())
        {
            $id = $row['id'];
            $book = $this->getBookByID($id);
            $books->append($book);
        }
        return $books;
    }
}
?>