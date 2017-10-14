#!/bin/python

class Certificates():
    def __init__(self, domain):
        self.domain = domain

    def add_temp_cert(self):
        '''
        Create a symbolic link to provide a temporary ssl certificate 
        for the new vhost untill a valid one has been installed
        '''
        if not os.path.isfile('/etc/nginx/ssl/' + self.domain + '/cert.pem') or not os.path.isfile('/etc/nginx/ssl/' + self.domain + '/privkey.pem'):
            logging.info('PROXY-LISTENER: Installing temporary SSL certificate for ' + self.domain + '.')

            cmd = [ 
                'mkdir -p /etc/nginx/ssl/' + self.domain, 
                'ln -s /etc/nginx/ssl/nginx.crt /etc/nginx/ssl/' + self.domain + '/cert.pem',
                'ln -s /etc/nginx/ssl/nginx.key /etc/nginx/ssl/' + self.domain + '/privkey.pem'
            ]
            
            Cmd().execute(cmd)

        logging.info('PROXY-LISTENER: Temporary certificate already present for ' + self.domain + '.')

    def add_letsencrypt_cert(self):
        '''
        Create a symbolic link to /etc/nginx/ssl for the obtained ssl certificate
        '''
        if os.path.isfile('/etc/letsencrypt/live/' + self.domain + '/cert.pem') or os.path.isfile('/etc/letsencrypt/live/' + self.domain + '/privkey.pem'):
            cmd = [
                'rm -f /etc/nginx/ssl/' + self.domain + '/*',
                'ln -s /etc/letsencrypt/live/' + self.domain + '/cert.pem /etc/nginx/ssl/' + self.domain + '/cert.pem',
                'ln -s /etc/letsencrypt/live/' + self.domain + '/privkey.pem /etc/nginx/ssl/' + self.domain + '/privkey.pem'
            ]
            Cmd().execute(cmd)

    def delete_cert():
        try:
            os.unlink('/etc/nginx/ssl/' + self.domain + '/cert.pem')
        except OSError as e:
            logging.info('PROXY-LISTENER: ' + str(e))

        try:
            os.unlink('/etc/nginx/ssl/' + self.domain + '/privkey.pem')
        except OSError as e:
            logging.info('PROXY-LISTENER: ' + str(e))

        Cmd().execute(['rm -f /etc/nginx/ssl/' + self.domain + '/*'])