//
// async_tcp_echo_server.cpp
// ~~~~~~~~~~~~~~~~~~~~~~~~~
//
// Copyright (c) 2003-2013 Christopher M. Kohlhoff (chris at kohlhoff dot com)
//
// Distributed under the Boost Software License, Version 1.0. (See accompanying
// file LICENSE_1_0.txt or copy at http://www.boost.org/LICENSE_1_0.txt)
//

#include <cstdlib>
#include <iostream>
#include <boost/bind.hpp>
#include <boost/asio.hpp>

using boost::asio::ip::tcp;
using namespace boost::asio;

class session
{
public:
    session(io_service& io_service) : socket_(io_service)
    {
    }
    
    tcp::socket& socket()
    {
        return socket_;
    }
    
    void start()
    {
        socket_.async_read_some(buffer(data_, max_length), bind(&session::handle_read, this, placeholders::error, placeholders::bytes_transferred));
    }
    
private:
    void handle_read(const boost::system::error_code& error, size_t bytes_transferred)
    {
        if (!error) {
            printf("Получено: %i\n", data_[0]);
            data_[0] = data_[0] * data_[0];
            async_write(socket_, buffer(data_, bytes_transferred), bind(&session::handle_write, this, placeholders::error));
        } else {
            delete this;
        }
    }
    
    void handle_write(const boost::system::error_code& error)
    {
        if (!error) {
            socket_.async_read_some(buffer(data_, max_length), bind(&session::handle_read, this, placeholders::error, placeholders::bytes_transferred));
        } else {
            delete this;
        }
    }
    
    tcp::socket socket_;
    enum { max_length = 1024 };
    char data_[max_length];
};

class server
{
public:
    server(io_service& io_service, short port) : io_service_(io_service), acceptor_(io_service, tcp::endpoint(tcp::v4(), port))
    {
        start_accept();
    }
    
private:
    void start_accept()
    {
        session* new_session = new session(io_service_);
        acceptor_.async_accept(new_session->socket(), bind(&server::handle_accept, this, new_session, placeholders::error));
    }
    
    void handle_accept(session* new_session,
                       const boost::system::error_code& error)
    {
        if (!error) {
            new_session->start();
        } else {
            delete new_session;
        }
        start_accept();
    }
    io_service& io_service_;
    tcp::acceptor acceptor_;
};

int main(int argc, char* argv[])
{
    try {
        if (argc != 2) {
            std::cerr << "Usage: async_tcp_echo_server <port>\n";
            return 1;
        }
        io_service io_service;
        server s(io_service, atoi(argv[1]));
        io_service.run();
    } catch (std::exception& e) {
        std::cerr << "Exception: " << e.what() << "\n";
    }
    
    return 0;
}