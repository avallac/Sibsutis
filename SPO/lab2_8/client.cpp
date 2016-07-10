#include <cstdlib>
#include <cstring>
#include <iostream>
#include <boost/asio.hpp>

using boost::asio::ip::tcp;

int main(int argc, char* argv[])
{
    char buf[1];
    try {
        if (argc != 4) {
            std::cerr << "client <host> <port> <num>\n";
            return 1;
        }
        boost::asio::io_service io_service;
        tcp::resolver resolver(io_service);
        tcp::resolver::query query(tcp::v4(), argv[1], argv[2]);
        tcp::resolver::iterator iterator = resolver.resolve(query);
        tcp::socket s(io_service);
        while(1) {
            buf[0] = argv[3][0] - '0';
            boost::asio::connect(s, iterator);
            boost::asio::write(s, boost::asio::buffer(buf, 1));
            size_t reply_length = boost::asio::read(s, boost::asio::buffer(buf, 1));
            printf("Ответ: %i\n", buf[0]);
            sleep(argv[3][0] - '0');
        }
    } catch (std::exception& e) {
        std::cerr << "Exception: " << e.what() << "\n";
    }
    
    return 0;
}